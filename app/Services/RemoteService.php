<?php

namespace App\Services;

use App\Models\Server;
use App\Models\Site;
use phpseclib3\Net\SFTP;
use Exception;
use stdClass;

class RemoteService
{
    protected SFTP $sftp;

    public function connect(Server $server)
    {
        $this->sftp = new SFTP($server->ip_address, $server->port);

        // Disable timeout so it waits for long downloads
        $this->sftp->setTimeout(0);

        if (!$this->sftp->login($server->username, $server->ssh_credentials)) {
            throw new Exception("Login Failed. Check credentials for server: {$server->name}");
        }

        return $this;
    }

    public function run(string $command)
    {
        return $this->sftp->exec($command);
    }

    public function deploySite(Site $site)
    {
        // Disable timeout
        set_time_limit(0);

        $folder = "my-sites/{$site->domain_name}";

        // Create Directories
        $this->sftp->mkdir($folder, -1, true);
        $this->sftp->mkdir("{$folder}/wp-content");

        // Generate JSON Config (Fail-proof)
        $dockerConfig = $this->generateDockerComposeJson($site);

        // Upload docker-compose.json
        $this->sftp->put("{$folder}/docker-compose.json", $dockerConfig);

        // Start Container using the JSON file
        // We use '-f docker-compose.json' to tell Docker to read the JSON file
        $command = "cd {$folder} && docker-compose -f docker-compose.json up -d --remove-orphans 2>&1";

        $output = $this->sftp->exec($command);

        return $output;
    }

    public function removeSite(Site $site)
    {
        $folder = "my-sites/{$site->domain_name}";

        // 1. Stop the site containers
        $this->sftp->exec("cd {$folder} && docker-compose -f docker-compose.json down");

        // 2. Force Delete Files using Docker
        // We use a temporary Alpine container to perform the deletion.
        // This bypasses "Permission Denied" errors because Docker runs as root.
        // We mount the parent 'my-sites' folder to /temp_work and delete the specific site folder.

        $deleteCmd = "docker run --rm -v \"$(pwd)/my-sites:/temp_work\" alpine rm -rf /temp_work/{$site->domain_name}";

        $this->sftp->exec($deleteCmd);
    }

    /**
     * Generates a JSON structure instead of YAML to avoid indentation errors.
     */
    private function generateDockerComposeJson(Site $site): string
    {
        $config = [
            'version' => '3.3',
            'services' => [
                'db' => [
                    'image' => 'mysql:5.7',
                    'container_name' => $site->container_name . '_db',
                    'volumes' => ['db_data:/var/lib/mysql'],
                    'restart' => 'always',
                    'environment' => [
                        'MYSQL_ROOT_PASSWORD' => $site->db_password,
                        'MYSQL_DATABASE' => $site->db_name,
                        'MYSQL_USER' => $site->db_user,
                        'MYSQL_PASSWORD' => $site->db_password,
                    ]
                ],
                'wordpress' => [
                    'image' => 'wordpress:latest',
                    'container_name' => $site->container_name,
                    'depends_on' => ['db'],
                    'ports' => ["{$site->port}:80"],
                    'restart' => 'always',
                    'environment' => [
                        'WORDPRESS_DB_HOST' => 'db',
                        'WORDPRESS_DB_USER' => $site->db_user,
                        'WORDPRESS_DB_PASSWORD' => $site->db_password,
                        'WORDPRESS_DB_NAME' => $site->db_name,
                    ],
                    'volumes' => ['./wp-content:/var/www/html/wp-content']
                ]
            ],
            'volumes' => [
                // Use stdClass to ensure this encodes as an object "db_data: {}" 
                // instead of an array "db_data: []"
                'db_data' => new stdClass()
            ]
        ];

        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

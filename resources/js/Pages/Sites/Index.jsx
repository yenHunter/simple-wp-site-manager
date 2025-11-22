import React from 'react';
import { Link, Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function SiteIndex({ server, sites }) {
    const { flash } = usePage().props;

    return (
        <AuthenticatedLayout>
            <Head title={`Sites on ${server.name}`} />

            <div className="flex justify-between items-center mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">Sites on {server.name}</h1>
                    <p className="text-sm text-gray-500">IP: {server.ip_address}</p>
                </div>
                <Link
                    href={route('servers.sites.create', server.id)}
                    className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                >
                    + New WordPress Site
                </Link>
            </div>

            {flash.message && (
                <div className="mb-4 bg-green-100 text-green-700 p-4 rounded">
                    {flash.message}
                </div>
            )}
            {flash.error && (
                <div className="mb-4 bg-red-100 text-red-700 p-4 rounded">
                    {flash.error}
                </div>
            )}

            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div className="p-6 bg-white border-b border-gray-200">
                    {sites.length === 0 ? (
                        <div className="text-center text-gray-500">No sites deployed yet.</div>
                    ) : (
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Port</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DB Info</th>
                                    <th className="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {sites.map((site) => (
                                    <tr key={site.id}>
                                        <td className="px-6 py-4 font-medium text-gray-900">
                                            <a href={`http://${server.ip_address}:${site.port}`} target="_blank" className="text-blue-600 hover:underline">
                                                {site.domain_name}
                                            </a>
                                        </td>
                                        <td className="px-6 py-4 text-gray-500">{site.port}</td>
                                        <td className="px-6 py-4">
                                            <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${site.status === 'running' ? 'bg-green-100 text-green-800' :
                                                    site.status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'
                                                }`}>
                                                {site.status.toUpperCase()}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-xs text-gray-500">
                                            DB: {site.db_name}<br />User: {site.db_user}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <Link
                                                href={route('servers.sites.destroy', [server.id, site.id])}
                                                method="delete"
                                                as="button"
                                                className="text-red-600 hover:text-red-900"
                                                onClick="return confirm('Are you sure? This will delete all files permanently.')"
                                            >
                                                Delete
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function SiteCreate({ server }) {
    const { data, setData, post, processing, errors } = useForm({
        domain_name: '',
        port: '', // Host port
        db_name: 'wp_db_' + Math.floor(Math.random() * 1000),
        db_user: 'wp_user',
        db_password: Math.random().toString(36).slice(-10),
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('servers.sites.store', server.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Deploy Site" />

            <div className="max-w-2xl mx-auto bg-white p-6 rounded shadow">
                <h2 className="text-xl font-bold mb-4">Deploy New WordPress Site</h2>
                <p className="mb-4 text-gray-600 text-sm">Deploying to: {server.name} ({server.ip_address})</p>

                <form onSubmit={submit}>
                    <div className="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">Domain / Folder Name</label>
                            <input
                                type="text"
                                placeholder="my-blog"
                                className="w-full border p-2 rounded"
                                value={data.domain_name}
                                onChange={e => setData('domain_name', e.target.value)}
                            />
                            <p className="text-xs text-gray-500 mt-1">Folder will be ~/my-sites/{data.domain_name || '...'}</p>
                            {errors.domain_name && <div className="text-red-500 text-sm">{errors.domain_name}</div>}
                        </div>
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">Port (Unique)</label>
                            <input
                                type="number"
                                placeholder="8082"
                                className="w-full border p-2 rounded"
                                value={data.port}
                                onChange={e => setData('port', e.target.value)}
                            />
                            <p className="text-xs text-gray-500 mt-1">Must be unique (e.g. 8082, 8083)</p>
                            {errors.port && <div className="text-red-500 text-sm">{errors.port}</div>}
                        </div>
                    </div>

                    <div className="border-t border-gray-200 pt-4 mt-4">
                        <h3 className="font-bold text-gray-700 mb-2">Database Configuration</h3>
                        <div className="grid grid-cols-1 gap-4">
                            <div>
                                <label className="block text-gray-700 text-sm">Database Name</label>
                                <input type="text" className="w-full border p-2 rounded" value={data.db_name} onChange={e => setData('db_name', e.target.value)} />
                            </div>
                            <div>
                                <label className="block text-gray-700 text-sm">Database User</label>
                                <input type="text" className="w-full border p-2 rounded" value={data.db_user} onChange={e => setData('db_user', e.target.value)} />
                            </div>
                            <div>
                                <label className="block text-gray-700 text-sm">Database Password</label>
                                <input type="text" className="w-full border p-2 rounded bg-gray-50" value={data.db_password} onChange={e => setData('db_password', e.target.value)} />
                            </div>
                        </div>
                    </div>

                    <div className="mt-6">
                        <button
                            type="submit"
                            disabled={processing}
                            className={`w-full bg-blue-600 text-white px-4 py-3 rounded font-bold hover:bg-blue-700 ${processing ? 'opacity-50 cursor-not-allowed' : ''}`}
                        >
                            {processing ? 'Deploying to Server (Please Wait)...' : 'Deploy WordPress'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
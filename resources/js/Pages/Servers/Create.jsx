import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ServerCreate() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        ip_address: '',
        port: 22,
        username: 'root',
        ssh_credentials: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('servers.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Add Server" />

            <div className="max-w-2xl mx-auto bg-white p-6 rounded shadow">
                <h2 className="text-xl font-bold mb-4">Connect to VPS</h2>

                <form onSubmit={submit}>
                    <div className="mb-4">
                        <label className="block text-gray-700">Server Name</label>
                        <input
                            type="text"
                            className="w-full border p-2 rounded mt-1"
                            value={data.name}
                            onChange={e => setData('name', e.target.value)}
                        />
                        {errors.name && <div className="text-red-500 text-sm">{errors.name}</div>}
                    </div>

                    <div className="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label className="block text-gray-700">IP Address</label>
                            <input
                                type="text"
                                className="w-full border p-2 rounded mt-1"
                                value={data.ip_address}
                                onChange={e => setData('ip_address', e.target.value)}
                            />
                            {errors.ip_address && <div className="text-red-500 text-sm">{errors.ip_address}</div>}
                        </div>
                        <div>
                            <label className="block text-gray-700">Port</label>
                            <input
                                type="number"
                                className="w-full border p-2 rounded mt-1"
                                value={data.port}
                                onChange={e => setData('port', e.target.value)}
                            />
                        </div>
                    </div>

                    <div className="mb-4">
                        <label className="block text-gray-700">SSH Username</label>
                        <input
                            type="text"
                            className="w-full border p-2 rounded mt-1"
                            value={data.username}
                            onChange={e => setData('username', e.target.value)}
                        />
                    </div>

                    <div className="mb-4">
                        <label className="block text-gray-700">SSH Password / Private Key</label>
                        <textarea
                            className="w-full border p-2 rounded mt-1 h-32 font-mono text-sm"
                            placeholder="Paste password or private key content..."
                            value={data.ssh_credentials}
                            onChange={e => setData('ssh_credentials', e.target.value)}
                        ></textarea>
                        {errors.ssh_credentials && <div className="text-red-500 text-sm">{errors.ssh_credentials}</div>}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                    >
                        {processing ? 'Saving...' : 'Save Server'}
                    </button>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
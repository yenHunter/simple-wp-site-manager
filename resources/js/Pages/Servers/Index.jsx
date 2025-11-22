import React from 'react';
import { Link, Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function ServerIndex({ servers }) {
    const { flash } = usePage().props; // Handle success messages (optional)

    return (
        <AuthenticatedLayout>
            <Head title="Servers" />

            <div className="flex justify-between items-center mb-6">
                <h1 className="text-2xl font-bold text-gray-800">Your Servers</h1>
                <Link
                    href={route('servers.create')}
                    className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                >
                    + Add Server
                </Link>
            </div>

            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div className="p-6 bg-white border-b border-gray-200">
                    {servers.length === 0 ? (
                        <div className="text-center text-gray-500">
                            No servers found. Add one to get started.
                        </div>
                    ) : (
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th className="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th className="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th className="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sites</th>
                                    <th className="px-6 py-3 bg-gray-50 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {servers.map((server) => (
                                    <tr key={server.id}>
                                        <td className="px-6 py-4 whitespace-nowrap font-medium">{server.name}</td>
                                        <td className="px-6 py-4 whitespace-nowrap">{server.ip_address}</td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <Link
                                                href={route('servers.sites.index', server.id)}
                                                className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer"
                                            >
                                                {server.sites_count} Sites
                                            </Link>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button className="text-red-600 hover:text-red-900">Delete</button>
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
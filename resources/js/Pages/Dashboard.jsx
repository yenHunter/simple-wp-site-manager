import React from 'react';

export default function Dashboard() {
    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100">
            <div className="p-6 bg-white rounded shadow-xl">
                <h1 className="text-3xl font-bold text-blue-600">
                    Setup Complete! 🚀
                </h1>
                <p className="mt-2 text-gray-600">
                    Laravel + Inertia + React + Tailwind is ready.
                </p>
            </div>
        </div>
    );
}
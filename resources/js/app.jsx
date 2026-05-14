import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Header from './components/Layout/Header';
import Footer from './components/Layout/Footer';

export default function App() {
    return (
        <div className="app-shell">
            <Header />
            <main className="main-content">
                <Routes>
                    <Route path="/" element={
                        <div className="hero-placeholder">
                            <h1>Next-Gen Electronics Store</h1>
                            <p>Storefront coming in Milestone 2.</p>
                        </div>
                    } />
                </Routes>
            </main>
            <Footer />
        </div>
    );
}

const container = document.getElementById('app');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <BrowserRouter>
                <App />
            </BrowserRouter>
        </React.StrictMode>
    );
}

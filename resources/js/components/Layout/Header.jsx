import React, { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { useCartStore } from '../../stores/useCartStore';
import { useAuthStore } from '../../stores/useAuthStore';

export default function Header() {
    const [menuOpen, setMenuOpen] = useState(false);
    const totalItems = useCartStore(s => s.totalItems());
    const user = useAuthStore(s => s.user);
    const logout = useAuthStore(s => s.logout);

    return (
        <header className="header">
            <div className="header-inner">
                {/* Brand */}
                <Link to="/" className="brand">
                    <span className="brand-icon">⚡</span>
                    <span className="brand-name">NextGen</span>
                </Link>

                {/* Primary Nav */}
                <nav className="nav-primary" aria-label="Main navigation">
                    <NavLink to="/products" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>Products</NavLink>
                    <NavLink to="/deals" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>Deals</NavLink>
                    <NavLink to="/compare" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>Compare</NavLink>
                </nav>

                {/* Actions */}
                <div className="header-actions">
                    {/* Search placeholder */}
                    <button className="icon-btn" aria-label="Search" title="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" width="20" height="20">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>

                    {/* Cart */}
                    <Link to="/cart" className="icon-btn cart-btn" aria-label="Cart">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" width="20" height="20">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        {totalItems > 0 && <span className="cart-badge">{totalItems}</span>}
                    </Link>

                    {/* Auth */}
                    {user ? (
                        <div className="user-menu">
                            <span className="user-name">{user.name}</span>
                            <button className="btn btn-ghost btn-sm" onClick={logout}>Sign out</button>
                        </div>
                    ) : (
                        <Link to="/login" className="btn btn-primary btn-sm">Sign in</Link>
                    )}
                </div>
            </div>
        </header>
    );
}

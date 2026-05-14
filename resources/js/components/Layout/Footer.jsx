import React from 'react';
import { Link } from 'react-router-dom';

export default function Footer() {
    return (
        <footer className="footer">
            <div className="footer-inner">
                <div className="footer-brand">
                    <span className="brand-icon">⚡</span>
                    <span className="brand-name">NextGen</span>
                    <p className="footer-tagline">High-performance electronics for demanding users.</p>
                </div>

                <div className="footer-links">
                    <div className="footer-col">
                        <h4>Shop</h4>
                        <Link to="/products">All Products</Link>
                        <Link to="/deals">Deals</Link>
                        <Link to="/compare">Compare</Link>
                    </div>
                    <div className="footer-col">
                        <h4>Account</h4>
                        <Link to="/orders">My Orders</Link>
                        <Link to="/wishlist">Wishlist</Link>
                        <Link to="/login">Sign In</Link>
                    </div>
                    <div className="footer-col">
                        <h4>Support</h4>
                        <Link to="/warranty">Warranty</Link>
                        <Link to="/contact">Contact</Link>
                    </div>
                </div>
            </div>
            <div className="footer-bottom">
                <p>© {new Date().getFullYear()} NextGen Electronics. All rights reserved.</p>
            </div>
        </footer>
    );
}

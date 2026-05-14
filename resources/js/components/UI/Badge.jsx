import React from 'react';

/**
 * Badge component for product status labels.
 * @param {'low-stock'|'out-of-stock'|'sale'|'new'|'verified'} variant
 */
export default function Badge({ children, variant = 'default', className = '' }) {
    return (
        <span className={`badge badge-${variant} ${className}`}>
            {children}
        </span>
    );
}

// Convenience exports for common badge types
export const LowStockBadge  = ({ count }) => <Badge variant="low-stock">Only {count} left!</Badge>;
export const OutOfStockBadge = ()          => <Badge variant="out-of-stock">Out of Stock</Badge>;
export const SaleBadge       = ({ pct })   => <Badge variant="sale">-{pct}%</Badge>;
export const NewBadge        = ()          => <Badge variant="new">New</Badge>;
export const VerifiedBadge   = ()          => <Badge variant="verified">✓ Verified Purchase</Badge>;

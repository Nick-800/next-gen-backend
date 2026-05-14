import React from 'react';

/**
 * Button component
 * @param {'primary'|'secondary'|'ghost'|'danger'} variant
 * @param {'sm'|'md'|'lg'} size
 */
export default function Button({
    children,
    variant = 'primary',
    size = 'md',
    disabled = false,
    loading = false,
    onClick,
    type = 'button',
    className = '',
    ...props
}) {
    return (
        <button
            type={type}
            onClick={onClick}
            disabled={disabled || loading}
            className={`btn btn-${variant} btn-${size} ${loading ? 'btn-loading' : ''} ${className}`}
            {...props}
        >
            {loading && <span className="btn-spinner" aria-hidden="true" />}
            {children}
        </button>
    );
}

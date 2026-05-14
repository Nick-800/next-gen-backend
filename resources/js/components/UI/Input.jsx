import React, { forwardRef } from 'react';

/**
 * Styled Input with label and error state.
 */
const Input = forwardRef(function Input({
    id,
    label,
    error,
    hint,
    type = 'text',
    required = false,
    className = '',
    ...props
}, ref) {
    return (
        <div className={`field ${error ? 'field-error' : ''} ${className}`}>
            {label && (
                <label htmlFor={id} className="field-label">
                    {label}
                    {required && <span className="field-required" aria-hidden="true">*</span>}
                </label>
            )}
            <input
                ref={ref}
                id={id}
                type={type}
                required={required}
                aria-invalid={!!error}
                aria-describedby={error ? `${id}-error` : hint ? `${id}-hint` : undefined}
                className="field-input"
                {...props}
            />
            {error && <p id={`${id}-error`} className="field-error-msg" role="alert">{error}</p>}
            {hint && !error && <p id={`${id}-hint`} className="field-hint">{hint}</p>}
        </div>
    );
});

export default Input;

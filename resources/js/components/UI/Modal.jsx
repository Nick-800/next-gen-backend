import React, { useEffect, useRef } from 'react';
import { createPortal } from 'react-dom';

export default function Modal({ isOpen, onClose, title, children, size = 'md' }) {
    const overlayRef = useRef(null);

    // Lock body scroll when open
    useEffect(() => {
        document.body.style.overflow = isOpen ? 'hidden' : '';
        return () => { document.body.style.overflow = ''; };
    }, [isOpen]);

    // Close on Escape key
    useEffect(() => {
        const handler = (e) => { if (e.key === 'Escape') onClose(); };
        if (isOpen) document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    return createPortal(
        <div
            className="modal-overlay"
            ref={overlayRef}
            onClick={(e) => { if (e.target === overlayRef.current) onClose(); }}
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
        >
            <div className={`modal modal-${size}`}>
                <div className="modal-header">
                    {title && <h2 id="modal-title" className="modal-title">{title}</h2>}
                    <button className="modal-close" onClick={onClose} aria-label="Close modal">✕</button>
                </div>
                <div className="modal-body">{children}</div>
            </div>
        </div>,
        document.body
    );
}

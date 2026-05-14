import React from 'react';
import { Link } from 'react-router-dom';
import { LowStockBadge, OutOfStockBadge } from './Badge';
import { useCartStore } from '../../stores/useCartStore';

/**
 * Product card for grid listings.
 * @param {{ id, name, price, image, slug, stockQuantity, lowStockThreshold, isOutOfStock }} variant
 */
export default function Card({ variant }) {
    const addItem = useCartStore(s => s.addItem);

    const showLowStock = !variant.isOutOfStock
        && variant.stockQuantity <= variant.lowStockThreshold
        && variant.stockQuantity > 0;

    const handleAddToCart = (e) => {
        e.preventDefault();
        if (!variant.isOutOfStock) {
            addItem({
                variantId: variant.id,
                sku:       variant.sku,
                name:      variant.name,
                price:     variant.price,
                isService: variant.isService ?? false,
                image:     variant.image,
            });
        }
    };

    return (
        <Link to={`/products/${variant.slug}`} className="card" aria-label={variant.name}>
            <div className="card-image-wrapper">
                {variant.image
                    ? <img src={variant.image} alt={variant.name} className="card-image" loading="lazy" />
                    : <div className="card-image-placeholder" aria-hidden="true" />
                }
                <div className="card-badges">
                    {variant.isOutOfStock && <OutOfStockBadge />}
                    {showLowStock && <LowStockBadge count={variant.stockQuantity} />}
                </div>
            </div>

            <div className="card-body">
                <h3 className="card-title">{variant.name}</h3>
                <p className="card-price">${Number(variant.price).toFixed(2)}</p>
            </div>

            <div className="card-footer">
                <button
                    className={`btn btn-primary btn-sm w-full ${variant.isOutOfStock ? 'btn-disabled' : ''}`}
                    onClick={handleAddToCart}
                    disabled={variant.isOutOfStock}
                    aria-label={variant.isOutOfStock ? 'Out of stock' : `Add ${variant.name} to cart`}
                >
                    {variant.isOutOfStock ? 'Out of Stock' : 'Add to Cart'}
                </button>
            </div>
        </Link>
    );
}

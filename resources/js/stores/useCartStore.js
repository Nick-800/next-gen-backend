import { create } from 'zustand';
import { persist } from 'zustand/middleware';

/**
 * Cart store — persisted to localStorage.
 * Never trust this for price/stock on the backend — always re-validate on checkout.
 */
export const useCartStore = create(
    persist(
        (set, get) => ({
            items: [],      // [{ variantId, sku, name, price, quantity, isService, image }]
            couponCode: '',
            couponDiscount: 0,

            addItem: (variant) => set((state) => {
                const existing = state.items.find(i => i.variantId === variant.variantId);
                if (existing) {
                    return {
                        items: state.items.map(i =>
                            i.variantId === variant.variantId
                                ? { ...i, quantity: i.quantity + 1 }
                                : i
                        ),
                    };
                }
                return { items: [...state.items, { ...variant, quantity: 1 }] };
            }),

            removeItem: (variantId) => set((state) => ({
                items: state.items.filter(i => i.variantId !== variantId),
            })),

            updateQuantity: (variantId, quantity) => set((state) => ({
                items: quantity <= 0
                    ? state.items.filter(i => i.variantId !== variantId)
                    : state.items.map(i => i.variantId === variantId ? { ...i, quantity } : i),
            })),

            clearCart: () => set({ items: [], couponCode: '', couponDiscount: 0 }),

            applyCoupon: (code, discount) => set({ couponCode: code, couponDiscount: discount }),

            // Computed values
            totalItems: () => get().items.reduce((sum, i) => sum + i.quantity, 0),
            subtotal: () => get().items.reduce((sum, i) => sum + i.price * i.quantity, 0),
            isBulkQuote: () => get().items.reduce((sum, i) => sum + i.quantity, 0) > 10,
            hasPhysicalItems: () => get().items.some(i => !i.isService),
        }),
        { name: 'nextgen-cart' }
    )
);

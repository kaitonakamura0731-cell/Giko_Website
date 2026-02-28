/**
 * Simple Shopping Cart using LocalStorage
 */

const Cart = {
    key: 'giko_cart',

    // Get all items
    getItems: function () {
        const stored = localStorage.getItem(this.key);
        return stored ? JSON.parse(stored) : [];
    },

    // Add item
    addItem: function (product) {
        // product: { id, name, price, image, options: { ... }, tradeInDiscount: number }
        let items = this.getItems();

        // Assign a unique timestamp-based ID for line-item management
        product.cartId = Date.now().toString();

        items.push(product);
        localStorage.setItem(this.key, JSON.stringify(items));
        this.updateBadge();

        // Optional: Show toast
        alert('カートに商品を追加しました');
    },

    // Remove item by cartId
    removeItem: function (cartId) {
        let items = this.getItems();
        items = items.filter((item) => item.cartId !== cartId);
        localStorage.setItem(this.key, JSON.stringify(items));
        this.updateBadge();
        // If we are on cart page, we might want to refresh the list
        if (window.renderCart) window.renderCart();
    },

    // Clear cart
    clear: function () {
        localStorage.removeItem(this.key);
        this.updateBadge();
    },

    // Get total price (with additional cost for no-buyback)
    getTotal: function () {
        const items = this.getItems();
        return items.reduce((total, item) => {
            // Price might be string like "66,000", remove commas
            let price =
                typeof item.price === 'string'
                    ? parseInt(item.price.replace(/,/g, ''))
                    : item.price;

            // Add additional cost if trade-in option exists but buyback is NOT selected
            if (this.hasTradeInOption(item) && !this.hasTradeIn(item)) {
                const additionalCost = this.getTradeInAmount(item);
                price = price + additionalCost;
            }

            return total + price;
        }, 0);
    },

    // Check if item has a trade-in option key at all (regardless of value)
    hasTradeInOption: function (item) {
        if (!item.options) return false;
        const tradeInKeys = [
            '下取り交換',
            '下取り',
            'トレードイン',
            '下取交換'
        ];
        for (const key of tradeInKeys) {
            if (item.options[key] !== undefined) {
                return true;
            }
        }
        return false;
    },

    // Check if item has trade-in buyback selected (あり)
    hasTradeIn: function (item) {
        if (!item.options) return false;
        // Check for various possible key names and values
        const tradeInKeys = [
            '下取り交換',
            '下取り',
            'トレードイン',
            '下取交換'
        ];
        const tradeInValues = ['あり', 'する', 'yes', 'true', '有'];

        for (const key of tradeInKeys) {
            if (item.options[key]) {
                const value = String(item.options[key]).toLowerCase();
                if (
                    tradeInValues.some((v) => value.includes(v.toLowerCase()))
                ) {
                    return true;
                }
            }
        }
        return false;
    },

    // Get the additional cost amount for an item (when buyback not selected)
    getTradeInAmount: function (item) {
        // Use per-product amount, fallback to 10000
        if (item.tradeInDiscount !== undefined && item.tradeInDiscount !== null) {
            return parseInt(item.tradeInDiscount) || 0;
        }
        return 10000; // Legacy fallback
    },

    // Get item price (with additional cost applied if no buyback)
    getItemPrice: function (item) {
        let price =
            typeof item.price === 'string'
                ? parseInt(item.price.replace(/,/g, ''))
                : item.price;

        if (this.hasTradeInOption(item) && !this.hasTradeIn(item)) {
            const additionalCost = this.getTradeInAmount(item);
            return price + additionalCost;
        }
        return price;
    },

    // Get additional cost amount for item (0 if buyback selected or no option)
    getItemAdditionalCost: function (item) {
        if (this.hasTradeInOption(item) && !this.hasTradeIn(item)) {
            return this.getTradeInAmount(item);
        }
        return 0;
    },

    // Legacy alias for backward compatibility
    getItemDiscount: function (item) {
        return this.getItemAdditionalCost(item);
    },

    // Update cart count badge in header (if exists)
    updateBadge: function () {
        const items = this.getItems();
        const count = items.length;
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach((el) => {
            el.innerText = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
    }
};

// Initialize badge on load
document.addEventListener('DOMContentLoaded', () => {
    Cart.updateBadge();
});

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
        // product: { id, name, price, image, options: { ... } }
        let items = this.getItems();

        // Improve: Check if same item with same options exists
        // For simplicity, we just push new item, or unique ID generation could be needed
        // Here we assign a unique timestamp-based ID for line-item management
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

    // Get total price (with trade-in discount)
    getTotal: function () {
        const items = this.getItems();
        return items.reduce((total, item) => {
            // Price might be string like "66,000", remove commas
            let price =
                typeof item.price === 'string'
                    ? parseInt(item.price.replace(/,/g, ''))
                    : item.price;

            // Apply 10,000 yen discount if trade-in option is selected
            if (this.hasTradeIn(item)) {
                price = Math.max(0, price - 10000);
            }

            return total + price;
        }, 0);
    },

    // Check if item has trade-in option selected
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

    // Get item price with discount applied
    getItemPrice: function (item) {
        let price =
            typeof item.price === 'string'
                ? parseInt(item.price.replace(/,/g, ''))
                : item.price;

        if (this.hasTradeIn(item)) {
            return Math.max(0, price - 10000);
        }
        return price;
    },

    // Get discount amount for item
    getItemDiscount: function (item) {
        return this.hasTradeIn(item) ? 10000 : 0;
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

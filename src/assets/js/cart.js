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

    // Get total price
    getTotal: function () {
        const items = this.getItems();
        return items.reduce((total, item) => {
            // Price might be string like "66,000", remove commas
            let price =
                typeof item.price === 'string'
                    ? parseInt(item.price.replace(/,/g, ''))
                    : item.price;
            return total + price;
        }, 0);
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

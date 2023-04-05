function featured_products_grid_shortcode($atts)
{
    ob_start();
    ?>
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-gap: 1rem;
        }

        .product-grid-item {
            border: 1px solid #ccc;
            padding: 1rem;
            text-align: center;
        }
    </style>

    <script>
		function addToCart(productId, quantityField) {
		  var quantity = quantityField.value;
		  var addToCartUrl = '<?php echo esc_url(wc_get_cart_url()); ?>?add-to-cart=' + productId + '&quantity=' + quantity;

		  window.location.href = addToCartUrl;
		}     	
        function filterProducts() {
            var category = document.getElementById("categoryFilter").value;
            var priceRange = document.getElementById("priceRangeFilter").value;

            var productItems = document.querySelectorAll(".product-grid-item");
            productItems.forEach(function (item) {
                var itemCategory = item.dataset.category;
                var itemPrice = parseFloat(item.dataset.price);

                var showItem = true;

                if (category !== "all" && itemCategory !== category) {
                    showItem = false;
                }

                if (priceRange === "0-5000" && (itemPrice < 0 || itemPrice > 5000)) {
                    showItem = false;
                } else if (priceRange === "5001-10000" && (itemPrice < 5001 || itemPrice > 10000)) {
                    showItem = false;
                } else if (priceRange === "10001-up" && itemPrice < 10001) {
                    showItem = false;
                }

                item.style.display = showItem ? "block" : "none";
            });
        }

    </script>

    <div class="product-filter">
        <select id="categoryFilter" onchange="filterProducts()">
            <option value="all">All Categories</option>
            <?php
            $categories = get_terms('product_cat');
            foreach ($categories as $category) {
                echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
            }
            ?>
        </select>

        <select id="priceRangeFilter" onchange="filterProducts()">
            <option value="all">All Prices</option>
            <option value="0-5000">0 - 5,000</option>
            <option value="5001-10000">5,001 - 10,000</option>
            <option value="10001-up">10,001 and up</option>
        </select>
    </div>

    <div class="product-grid">
        <?php
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );
        $loop = new WP_Query($args);
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            $categories = wp_get_post_terms(get_the_ID(), 'product_cat');
            $category_slugs = array();
            foreach ($categories as $category) {
                $category_slugs[] = $category->slug;
            }
        ?>
            <div class="product-grid-item" data-category="<?php echo implode(' ', $category_slugs); ?>" data-price="<?php echo $product->get_regular_price(); ?>">
                <?php the_post_thumbnail('woocommerce_thumbnail'); ?>
                <h2><?php the_title(); ?></h2>
                <p><?php echo wc_price($product->get_regular_price()); ?></p>
                                <input type="number" min="1" max="999999" value="1" class="quantity">
                <button onclick="addToCart(<?php echo get_the_ID(); ?>, this.previousElementSibling)">Add to Cart</button>


            </div>
        <?php
        endwhile;
        wp_reset_query();
        ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('featured_products_grid', 'featured_products_grid_shortcode');

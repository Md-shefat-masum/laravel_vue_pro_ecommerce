<template>
    <div class="container">
        <div class="card list_card">
            <div class="card-header">
                <h4>Details</h4>
                <div class="btns">
                    <a href="" @click.prevent="call_store(`set_${store_prefix}`,null), $router.push({ name: `All${route_prefix}` })"  class="btn rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        <span >
                            Back
                        </span>
                    </a>
                </div>
            </div>
            <div class="card-body pb-5 ">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div v-if="product" class="container">
                            <div class="text-center">
                                <img v-for="(image,index) in product.related_images" :key="index" :src="'/'+image.image" style="margin:5px;width: 150px">
                            </div>
                            <div class="py-5">
                                <h4>Details</h4>
                                <hr>
                                <table class="table details_table">
                                    <tbody>
                                        <tr>
                                            <th>Title</th>
                                            <td>:</td>
                                            <td>{{ product.product_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Search keywords</th>
                                            <td>:</td>
                                            <td>{{ product.product_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Stock</th>
                                            <td>:</td>
                                            <td>{{ product.stocks_sum_qty }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Sold</th>
                                            <td>:</td>
                                            <td>{{ product.sales_sum_qty }}</td>
                                        </tr>
                                        <tr>
                                            <th>Current Stock</th>
                                            <td>:</td>
                                            <td>{{ product.stocks_sum_qty - product.sales_sum_qty }}</td>
                                        </tr>
                                        <tr>
                                            <th>Low Stock</th>
                                            <td>:</td>
                                            <td>{{ product.track_inventory_on_the_variant_level_low_stock }}</td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
                                            <td>:</td>
                                            <td>{{ product.default_price }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount Price</th>
                                            <td>:</td>
                                            <td>{{ product.discount && product.discount.discount_amount }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount %</th>
                                            <td>:</td>
                                            <td>{{ product.discount && product.discount.discount_percent }}</td>
                                        </tr>
                                        <tr>
                                            <th>Discount End</th>
                                            <td>:</td>
                                            <td>
                                                <span v-if="product.discount">
                                                    {{ new Date(product.discount.discount_last_date ).toDateString() }},
                                                    {{ new Date(product.discount.discount_last_date ).toLocaleTimeString() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Brand</th>
                                            <td>:</td>
                                            <td>{{ product.brand && product.brand.name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Categories</th>
                                            <td>:</td>
                                            <td>
                                                <div v-if="product.categories.length">
                                                    <span v-for="(cat, index) in product.categories" :key="cat.id">
                                                        {{ cat.name }}

                                                        <i v-if="index < product.categories.length - 1">, &nbsp;</i>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created at</th>
                                            <td>:</td>
                                            <td>
                                                {{ new Date(product.created_at ).toDateString() }},
                                                {{ new Date(product.created_at ).toLocaleTimeString() }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Updated at</th>
                                            <td>:</td>
                                            <td>
                                                {{ new Date(product.updated_at ).toDateString() }},
                                                {{ new Date(product.updated_at ).toLocaleTimeString() }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mt-4 v_html">
                                    <h4>Description</h4>
                                    <div v-html="product.description"></div>
                                </div>
                                <div class="mt-4 v_html">
                                    <h4>Specification</h4>
                                    <div v-html="product.specification"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <permission-button
                    :permission="'can_edit'"
                    :to="{name:`Edit${route_prefix}`,params:{id: $route.params.id}}"
                    :classList="'btn btn-outline-info'">
                    <i class="fa text-info fa-pencil"></i> &nbsp;
                    Edit
                </permission-button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import PermissionButton from '../components/PermissionButton.vue'
/** store and route prefix for export object use */
import PageSetup from './PageSetup';
const {route_prefix, store_prefix} = PageSetup;

export default {
    components: { PermissionButton },
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
        }
    },
    created: function () {
        this[`fetch_${store_prefix}`]({id: this.$route.params.id, select_all:1})

        // setTimeout(() => {
        //     document.querySelector("section").style.background = "transparent"
        //     // if(this[`get_${store_prefix}`].description.includes("bg-white")) {
        //     //     document.querySelector("section").style.background-color = "transparent";
        //     // }
        // }, 1000);

    },
    updated: function(){
        [
            ".v_html h2",
            ".v_html span",
            ".v_html p",
            ".v_html div",
            ".v_html table",
            ".v_html table tbody",
            ".v_html table thead",
            ".v_html table tr",
            ".v_html table tr td",
            ".v_html table tr th",
            ".v_html strong",
            ".v_html figure",
            ".v_html section",
        ].forEach(i=>document.querySelectorAll(i).forEach(i=>i.setAttribute('style','')));
        [...document.querySelectorAll('.v_html table')]
            .forEach(i=>{
                i.setAttribute('class','table text-start');
            })
    },
    methods: {
        ...mapActions([
            `fetch_${store_prefix}`,
        ]),
        ...mapMutations([
            `set_${store_prefix}`
        ]),
        call_store: function(name, params=null){
            this[name](params)
        },
    },
    computed: {
        ...mapGetters({
            product: `get_${store_prefix}`,
        })
    }
}
</script>

<style scoped>
    .details_table tbody tr th:nth-child(1){
        width: 200px;
    }
    .details_table tbody tr td:nth-child(2){
        width: 3px;
    }
    .details_table tbody tr td:nth-child(3){
        text-align: left;
    }

    .list_card .v_html tbody tr td{
        text-align: left;
    }
</style>

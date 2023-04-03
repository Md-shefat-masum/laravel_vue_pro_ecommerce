<template>
    <div class="container">
        <div class="card list_card">
            <div class="card-header">
                <h4>Create</h4>
                <div class="btns">
                    <router-link :to="{ name: `All${route_prefix}` }" class="btn rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        Back
                    </router-link>
                </div>
            </div>
            <form @submit.prevent="store_categorys($event.target)" autocomplete="false">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="admin_form form_1">
                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Name`"
                                        :name="`name`"
                                        :keyup="make_slug"
                                    />
                                </div>
                                <div class="form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Url`"
                                        :name="`url`"
                                        :value="typeof slug == 'string'?slug:''"
                                        :keyup="make_slug"
                                    />
                                </div>
                                <div class="form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description"></textarea>
                                </div>

                                <div class="form-group d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Image`"
                                        :name="`category_image`"
                                        :type="`file`"
                                        :preview="true"
                                    />
                                </div>

                                <div class="form-group full_width category_card_dropdown custom_scroll" >
                                    <label for="" class="mb-2">Select Category Parent</label>
                                    <cat-list-radio :list="nested_cats"></cat-list-radio>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-outline-info" >
                        <i class="fa fa-upload"></i>
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import debounce from 'debounce';
import { mapActions, mapGetters } from 'vuex'
import InputField from '../components/InputField.vue'
import CatListRadio from './components/CatListRadio.vue';
/** store and route prefix for export object use */
import PageSetup from './PageSetup';
const {route_prefix, store_prefix} = PageSetup;

export default {
    components: { InputField, CatListRadio },
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
            slug:'',
        }
    },
    created: async function () {
        await this[`fetch_${store_prefix}_all_json`]();
        // console.log(this.nested_cats);
    },
    methods: {
        ...mapActions([
            `store_${store_prefix}`,
            `fetch_${store_prefix}_all_json`,
            `fetch_${store_prefix}_check_exists`,
            `generateSlug`,
        ]),
        store_categorys: async function(target){
            await this[`store_${store_prefix}`](target);
            await this[`fetch_${store_prefix}_all_json`]();
        },
        call_store: function(name, params=null){
            this[name](params)
        },
        make_slug: debounce( async function({value}){
            this.slug = '/'+ await this.generateSlug(value);
            let check = await this[`fetch_${store_prefix}_check_exists`](this.slug);
            // console.log(this.slug, check);
        },1000),

    },
    computed: {
        ...mapGetters({
            nested_cats: `get_${store_prefix}_all_json_nested`,
        })
    }
};
</script>

<style>
</style>

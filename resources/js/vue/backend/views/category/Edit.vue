<template>
    <div class="container">
        <div class="card list_card">
            <div class="card-header">
                <h4>Edit</h4>
                <div class="btns">
                    <router-link :to="{ name: `All${route_prefix}` }" class="btn rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        Back
                    </router-link>
                </div>
            </div>
            <form v-if="category" @submit.prevent="update_category($event.target)" enctype="multipart/form-data" autocomplete="false">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="admin_form form_1">
                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Name`"
                                        :name="`name`"
                                        :value="category.name"
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
                                    <textarea :value="category.description" class="form-control" id="description" name="description"></textarea>
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
        await this[`fetch_${store_prefix}`]({id: this.$route.params.id});
        this.set_data();
        // console.log(this.nested_cats);
    },
    methods: {
        ...mapActions([
            `update_${store_prefix}`,
            `fetch_${store_prefix}`,
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
        set_data: function(){
            this.slug = this.category.slug;
            document.querySelector('.file_preview').innerHTML = `
                <img src="/${this.category.category_image}" />
            `;
            this.check_selected_categories();
        },
        check_selected_categories: function(){
            setTimeout(() => {
                if(this.category.parent_id){
                    document.querySelector(`input[data-id="${this.category.id}"]`).checked=true;
                }
                let el = document.querySelector(`input[data-id="${this.category.id}"]`);
                if(el){
                    (function check_parent(el){
                        if(el.parentNode){
                            if(el.parentNode.classList?.contains('list')){
                                el.parentNode.classList.add('d-block');
                            }
                            check_parent(el.parentNode)
                        }
                    })(el)
                }
            }, 500);
        }
    },
    computed: {
        ...mapGetters({
            nested_cats: `get_${store_prefix}_all_json_nested`,
            category: `get_${store_prefix}`,
        })
    }
};
</script>

<style>
</style>

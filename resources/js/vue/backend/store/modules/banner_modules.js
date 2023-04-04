
import axios from "axios";
import StoreModule from "./schema/StoreModule";

let test_module = new StoreModule('banner','banner','Banner');
const {store_prefix, api_prefix, route_prefix} = test_module;

// state list
const state = {
    ...test_module.states(),
};

// get state
const getters = {
    ...test_module.getters(),
};

// actions

const actions = {
    ...test_module.actions(),
    [`fetch_${store_prefix}_toggle_status`]: async function({state,commit},id){
        let res = await axios.post('/banner/toggle-status',{id})
        if(res.data){
            window.s_alert("banner show");
        }else{
            window.s_alert("banner hide");

        }
        return res.data;
    },
}

// mutators
// console.log(test_module.mutations());
const mutations = {
    ...test_module.mutations(),

};


export default {
    state,
    getters,
    actions,
    mutations,
};

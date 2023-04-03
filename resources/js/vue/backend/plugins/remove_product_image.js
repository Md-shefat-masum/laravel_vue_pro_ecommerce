import axios from "axios";

window.remove_product_image = async function(event, id){
    event.preventDefault();
    let cconfirm = await window.s_confirm("Delete image");
    if(cconfirm){
        await axios.post('/product/delete-related-image',{id})
            .then(res=>{
                event.target.parentNode.innerHTML = "";
            })
    }
}

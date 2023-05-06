const finalPosts = [];
const postList = fetch("tell-js-posts.php").then((response) => {
    if (!response.ok) {
        throw new Error("Something went wrong!");
    }
    return response.json();
}).then((data) => {
    data.forEach(element => {
        finalPosts.push(element);
    })
});

console.log(finalPosts);

const imgs=document.querySelectorAll('#immagine').forEach((item, index)=>{
    item.addEventListener('click', () => {
        console.log(finalPosts[index]["id_post"]);
        console.log(index);
    });
});
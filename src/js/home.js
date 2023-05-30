/* IMPORTANT Needs post-utils.js to work */
const timestamp = generateDate()

let post_ids = []
document.querySelectorAll('[id^="post-card-"]').forEach(post => {
    let id = post.id.split("-")[2]
    //post_ids.push(post.id.split("-")[2]) /* TODO necessary? */
    getPostLiked(id)
    getPostSaved(id)
    attachLike(id)
    attachSave(id)
    attachNewComment(id)
})


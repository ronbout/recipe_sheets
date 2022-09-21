import React from "react";

function RelatedPosts({ venueObj }) {
  const relatedPosts = venueObj.related_posts;

  const postCards = relatedPosts.map((post) => {
    const postImg = post.post_thumbnail.replace("class=", "className=");
    const postDate = post.post_date.split(" ")[0];
    return (
      <div key={post.post_id} style={{ width: "48%" }} className="card my-2">
        <div className="row">
          <div
            className="Container col-sm-6"
            dangerouslySetInnerHTML={{ __html: postImg }}
          ></div>
          <div className="col-sm-6 pt-4">
            <p className="card-text">Posted Date: {postDate}</p>
            <p className="card-text">Author: {post.post_author}</p>
          </div>
        </div>
        <div className="card-body">
          <h5 className="card-title">{post.post_title}</h5>
          <p className="card-text">{post.post_excerpt}</p>
          <a href={post.post_link} target="_blank" rel="noreferrer">
            <button type="button" className="btn btn-primary">
              View Post
            </button>
          </a>
        </div>
      </div>
    );
  });
  return (
    <div className="container text-center p-4">
      <h2>Related Posts</h2>
      <div className="posts-container d-flex justify-content-between flex-wrap">
        {postCards}
      </div>
    </div>
  );
}

export default RelatedPosts;

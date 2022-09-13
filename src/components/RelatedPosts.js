import React from "react";

function RelatedPosts({ venueObj }) {
  const relatedPosts = venueObj.related_posts;
  const postCards = relatedPosts.map((postId) => {
    return (
      <div className="card w-50 my-2">
        <div className="card-body">
          <h5 className="card-title">Post Title will Go here: {postId}</h5>
          <p className="card-text">This will be the Post excerpt</p>
          <button type="button" className="btn btn-primary">
            View Post
          </button>
        </div>
      </div>
    );
  });
  return (
    <div className="container p-4">
      <h2>Related Posts</h2>
      <div className="posts-container d-flex flex-wrap">{postCards}</div>
    </div>
  );
}

export default RelatedPosts;

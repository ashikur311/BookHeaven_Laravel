@php
  // Expect from controller:
  // $community (assoc), $community_id, $current_user_avatar, $current_user_role
  // $members (array of users except current), $unread_counts (sender_id=>count)
  // $posts (array of prepared posts), $current_username
@endphp

@include('partials.header')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ isset($community) ? e($community['name']) : 'Community' }} | Book Heaven</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/BookHeaven2.0/css/community_dashboard.css">
</head>
<body>
  <main>
    <aside>
      <div class="community-info-card">
        @if (isset($community))
          <img src="{{ $community['cover_image_url'] }}" alt="Community Cover" class="community-cover">
          <div class="community-details">
            <h2>{{ $community['name'] }}</h2>
            <p>{{ $community['description'] }}</p>

            <div class="community-creator">
              <img src="{{ $community['creator_avatar'] }}" alt="Creator" class="creator-avatar">
              <div class="creator-info">
                <div>Created by {{ $community['creator_name'] }}</div>
                <small>{{ $community['created_ago'] ?? '' }}</small>
              </div>
            </div>
          </div>
        @endif
      </div>

      <div class="members-card">
        <div class="members-header">
          <h3>Members</h3>
          <span class="member-count">{{ (isset($members) ? count($members) : 0) + 1 }}</span>
        </div>

        <div class="member-list">
          <div class="member-item">
            <div class="member-info">
              <img src="{{ $current_user_avatar }}" alt="You" class="member-avatar">
              <div>
                <div class="member-name">You ({{ ucfirst($current_user_role ?? 'member') }})</div>
              </div>
            </div>
          </div>

          @if (!empty($members))
            @foreach ($members as $member)
              <div class="member-item">
                <div class="member-info">
                  <img
                    src="{{ !empty($member['user_profile']) ? '/BookHeaven2.0/' . e($member['user_profile']) : 'https://via.placeholder.com/40' }}"
                    alt="{{ e($member['username']) }}" class="member-avatar">
                  <div>
                    <div class="member-name">{{ $member['username'] }}</div>
                    <div class="member-role">{{ ucfirst($member['role']) }}</div>
                  </div>
                </div>
                <button class="message-btn"
                        onclick="window.location.href='{{ route('community.messages', ['c_id' => $community_id, 'u_id' => $member['user_id']]) }}'">
                  <i class="fas fa-envelope"></i>
                  @if (isset($unread_counts[$member['user_id']]) && $unread_counts[$member['user_id']] > 0)
                    <span class="unread-count">{{ $unread_counts[$member['user_id']] }}</span>
                  @endif
                </button>
              </div>
            @endforeach
          @else
            <p>No other members found</p>
          @endif
        </div>
      </div>
    </aside>

    <div class="dashboard-content">
      @if (isset($community))
        <div class="create-post">
          <form class="post-form" method="post" enctype="multipart/form-data" action="{{ route('community.posts.store', ['id' => $community_id]) }}">
            @csrf
            <textarea name="content" class="post-input" placeholder="What's on your mind?" required></textarea>
            <div class="post-actions">
              <div>
                <input type="file" id="post-image" name="post_image" class="file-input" accept="image/*">
                <label for="post-image" class="file-label">
                  <i class="fas fa-image"></i> Add Image
                </label>
              </div>
              <button type="submit" name="create_post" class="post-submit">Post</button>
            </div>
          </form>
        </div>

        <div class="posts-container">
          @if (!empty($posts))
            @foreach ($posts as $post)
              <div class="post-card" id="post-{{ $post['id'] }}">
                @if (!empty($post['can_edit']))
                  <div class="post-options">
                    <button class="options-btn" onclick="toggleOptions(this)">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="options-menu">
                      <div class="option-item" onclick="editPost({{ $post['id'] }}, `{{ addslashes($post['content']) }}`)">
                        <i class="fas fa-edit"></i> Edit
                      </div>
                      <div class="option-item delete" onclick="deletePost({{ $post['id'] }})">
                        <i class="fas fa-trash"></i> Delete
                      </div>
                    </div>
                  </div>
                @endif

                <div class="post-header">
                  <img src="{{ $post['avatar'] }}" alt="{{ $post['user'] }}" class="user-avatar">
                  <div class="user-info">
                    <div class="user-name">{{ $post['user'] }}</div>
                    <div class="post-time">{{ $post['time'] }}</div>
                  </div>
                </div>
                <div class="post-content" id="post-content-{{ $post['id'] }}">{!! $post['content'] !!}</div>

                @if (!empty($post['image_url']))
                  <img src="{{ $post['image_url'] }}" alt="Post image" class="post-image">
                @endif

                <div class="post-footer">
                  <button class="action-btn like-btn {{ $post['is_liked'] ? 'liked' : '' }}"
                          onclick="toggleLike({{ $post['id'] }}, this)">
                    <i class="fas fa-thumbs-up"></i>
                    <span>{{ $post['likes'] }} Likes</span>
                  </button>
                  <button class="action-btn comment-btn" onclick="toggleComments({{ $post['id'] }})">
                    <i class="fas fa-comment"></i>
                    <span>{{ $post['comment_count'] }} Comments</span>
                  </button>
                </div>

                <div class="comment-section" id="comments-{{ $post['id'] }}">
                  <form class="comment-form" method="post" onsubmit="return addComment(event, {{ $post['id'] }})">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post['id'] }}">
                    <input type="text" name="content" class="comment-input" placeholder="Write a comment..." required>
                    <button type="submit" name="add_comment" class="comment-submit">Post</button>
                  </form>

                  <div class="comments-list" id="comments-list-{{ $post['id'] }}">
                    @foreach ($post['comments'] as $comment)
                      <div class="comment-item">
                        <img src="{{ $comment['avatar'] }}" alt="{{ $comment['user'] }}" class="comment-avatar">
                        <div class="comment-content">
                          <div class="comment-header">
                            <div class="comment-user">{{ $comment['user'] }}</div>
                            <div class="comment-time">{{ $comment['time'] }}</div>
                          </div>
                          <div class="comment-text">{!! $comment['content'] !!}</div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            @endforeach
          @else
            <div class="post-card"><div class="post-content"><p>No posts yet. Be the first to post in this community!</p></div></div>
          @endif
        </div>
      @else
        <div class="post-card"><div class="post-content"><p>Community not found or you don't have access to view it.</p></div></div>
      @endif
    </div>
  </main>

  {{-- Edit Post Modal --}}
  <div class="modal-overlay" id="editModal">
    <div class="edit-modal">
      <div class="modal-header">
        <div class="modal-title">Edit Post</div>
        <button class="close-btn" onclick="closeModal()">&times;</button>
      </div>
      <form class="edit-form" onsubmit="return savePost(event)">
        @csrf
        <input type="hidden" id="edit-post-id">
        <textarea id="edit-post-content" class="edit-textarea" required></textarea>
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
          <button type="submit" class="save-btn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.footer')

  <script>
    function toggleLike(postId, button){
      const formData = new FormData();
      formData.append('post_id', postId);
      formData.append('_token', '{{ csrf_token() }}');

      fetch('{{ route('community.posts.toggle-like') }}', {
        method:'POST',
        body: formData
      }).then(res=>{
        if(res.ok){
          const likeCount = button.querySelector('span');
          const current = parseInt(likeCount.textContent);
          if(button.classList.contains('liked')){
            button.classList.remove('liked');
            likeCount.textContent = (current - 1) + ' Likes';
          }else{
            button.classList.add('liked');
            likeCount.textContent = (current + 1) + ' Likes';
          }
        }
      }).catch(console.error);
    }

    function toggleComments(postId){
      const el = document.getElementById('comments-' + postId);
      el.style.display = el.style.display === 'block' ? 'none' : 'block';
      if(el.style.display === 'block') el.scrollIntoView({behavior:'smooth', block:'nearest'});
    }

    function addComment(e, postId){
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      formData.append('_token', '{{ csrf_token() }}');

      fetch('{{ route('community.posts.comment') }}', { method:'POST', body: formData })
        .then(r=>r.json())
        .then(data=>{
          if(data.success){
            form.querySelector('input[type="text"]').value = '';
            const commentElement = document.createElement('div');
            commentElement.className = 'comment-item';
            commentElement.innerHTML = `
              <img src="${data.comment.avatar}" alt="${data.comment.user}" class="comment-avatar">
              <div class="comment-content">
                <div class="comment-header">
                  <div class="comment-user">${data.comment.user}</div>
                  <div class="comment-time">${data.comment.time}</div>
                </div>
                <div class="comment-text">${data.comment.content}</div>
              </div>
            `;
            document.getElementById('comments-list-' + postId).appendChild(commentElement);

            const btn = document.querySelector(\`.comment-btn[onclick="toggleComments(${postId})"]\`);
            if(btn){
              const span = btn.querySelector('span');
              if(span){
                const n = parseInt(span.textContent);
                span.textContent = (n + 1) + ' Comments';
              }
            }
          }else{
            alert(data.error || 'Failed to add comment');
          }
        })
        .catch(err=>{ console.error(err); alert('An error occurred while adding the comment'); });

      return false;
    }

    function toggleOptions(button){
      const menu = button.nextElementSibling;
      menu.classList.toggle('show');
      document.querySelectorAll('.options-menu').forEach(other=>{
        if(other !== menu && other.classList.contains('show')) other.classList.remove('show');
      });
    }

    document.addEventListener('click', function(e){
      if(!e.target.closest('.post-options')){
        document.querySelectorAll('.options-menu').forEach(m=>m.classList.remove('show'));
      }
    });

    function deletePost(postId){
      if(!confirm('Are you sure you want to delete this post?')) return;
      const fd = new FormData();
      fd.append('post_id', postId);
      fd.append('_token', '{{ csrf_token() }}');

      fetch('{{ route('community.posts.delete') }}', { method:'POST', body: fd })
        .then(r=>r.json())
        .then(data=>{
          if(data.success){ document.getElementById(`post-${postId}`).remove(); }
          else { alert(data.error || 'Failed to delete post'); }
        })
        .catch(err=>{ console.error(err); alert('An error occurred while deleting the post'); });
    }

    function editPost(postId, content){
      document.getElementById('edit-post-id').value = postId;
      document.getElementById('edit-post-content').value = content.replace(/\\/g, '');
      document.getElementById('editModal').classList.add('show');
    }

    function closeModal(){ document.getElementById('editModal').classList.remove('show'); }

    function savePost(e){
      e.preventDefault();
      const postId = document.getElementById('edit-post-id').value;
      const content = document.getElementById('edit-post-content').value;

      const fd = new FormData();
      fd.append('post_id', postId);
      fd.append('content', content);
      fd.append('_token', '{{ csrf_token() }}');

      fetch('{{ route('community.posts.update') }}', { method:'POST', body: fd })
        .then(r=>r.json())
        .then(data=>{
          if(data.success){
            document.getElementById(`post-content-${postId}`).innerHTML = data.content;
            closeModal();
          }else{
            alert(data.error || 'Failed to update post');
          }
        })
        .catch(err=>{ console.error(err); alert('An error occurred while updating the post'); });

      return false;
    }

    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('.comment-section').forEach(s=> s.style.display='none');
    });
  </script>
</body>
</html>

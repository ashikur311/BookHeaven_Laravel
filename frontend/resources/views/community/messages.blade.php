{{-- resources/views/community/messages.blade.php --}}
@php
  // Expect from controller:
  // $community_id, $recipient_id, $user_id
  // $community (assoc), $community_members (array), $current_recipient (assoc or null), $messages (array)
@endphp

@include('partials.header')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ isset($community['name']) ? e($community['name']) : 'Community Messages' }}</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/BookHeaven2.0/css/messages.css">
</head>
<body>
  <main>
    <div class="members-sidebar">
      <div class="community-header">
        <button class="back-btn" onclick="window.location.href='{{ route('community.show',['id'=>$community_id]) }}'">
          <i class="fas fa-arrow-left"></i>
        </button>

        @if (!empty($community['community_id']))
          <div class="community-info">
            @if (!empty($community['cover_image_url']))
              <img src="{{ e($community['cover_image_url']) }}" alt="{{ e($community['name']) }}" class="community-image">
            @else
              <div class="community-image"
                   style="background-color:#{{ isset($community['name']) ? substr(md5($community['name']),0,6) : 'd41d8c' }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">
                {{ isset($community['name']) ? substr($community['name'],0,2) : 'CM' }}
              </div>
            @endif
            <div class="community-title">{{ isset($community['name']) ? e($community['name']) : 'Community' }}</div>
          </div>
        @endif
      </div>

      <div class="members-list" id="membersList">
        @if (!empty($community_members))
          @foreach ($community_members as $member)
            @if ((int)$member['user_id'] === (int)$user_id) @continue @endif

            @php
              // Controller can attach $member['last_message'] = ['content'=>..., 'created_at'=>..., 'sender_id'=>...]
              $last_message = $member['last_message'] ?? null;
            @endphp

            <div class="member-item {{ ($current_recipient && $member['user_id'] == $current_recipient['user_id']) ? 'active' : '' }}"
                 data-user-id="{{ $member['user_id'] }}"
                 onclick="window.location.href='{{ route('community.messages', ['u_id'=>$member['user_id'],'c_id'=>$community_id]) }}'">
              @if (!empty($member['user_profile']))
                <img src="/BookHeaven2.0/{{ e($member['user_profile']) }}" alt="{{ e($member['username']) }}" class="member-avatar">
              @else
                <div class="member-avatar"
                     style="background-color:#{{ substr(md5($member['username']),0,6) }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">
                  {{ substr($member['username'],0,2) }}
                </div>
              @endif

              <div class="member-info">
                <div class="member-name">
                  {{ $member['username'] }}
                  @if ($last_message)
                    <span class="member-time">{{ $last_message['time_for_list'] ?? '' }}</span>
                  @endif
                </div>
                @if ($last_message)
                  <div class="member-last-message">
                    {{ ($last_message['sender_id'] ?? 0) == $user_id ? 'You: ' : '' }}
                    {{ \Illuminate\Support\Str::limit($last_message['content'] ?? '', 30) }}
                  </div>
                @endif
              </div>

              @if (!empty($member['unread_count']))
                <div class="unread-count" id="unreadCount-{{ $member['user_id'] }}">{{ $member['unread_count'] }}</div>
              @endif

              <div class="member-status-indicator {{ strtolower($member['user_status'] ?? 'offline') }}"></div>
            </div>
          @endforeach
        @else
          <div class="no-members">No members found in this community</div>
        @endif
      </div>
    </div>

    @if ($current_recipient)
      <div class="chat-container">
        <div class="chat-header">
          <div class="recipient-info">
            @if (!empty($current_recipient['user_profile']))
              <img src="/BookHeaven2.0/{{ e($current_recipient['user_profile']) }}" alt="{{ e($current_recipient['username']) }}" class="recipient-avatar">
            @else
              <div class="recipient-avatar"
                   style="background-color:#{{ substr(md5($current_recipient['username']),0,6) }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;">
                {{ substr($current_recipient['username'],0,2) }}
              </div>
            @endif
            <div>
              <div class="recipient-name">{{ e($current_recipient['username']) }}</div>
              <div class="recipient-status">
                <span class="status-indicator {{ strtolower($current_recipient['user_status'] ?? 'offline') }}"></span>
                {{ ucfirst($current_recipient['user_status'] ?? 'offline') }}
              </div>
            </div>
          </div>
        </div>

        <div class="messages-container" id="messagesContainer">
          @foreach ($messages as $message)
            @php $is_me = ((int)($message['sender_id'] ?? 0) === (int)$user_id); @endphp
            <div class="message {{ $is_me ? 'message-me' : 'message-them' }}" data-message-id="{{ $message['message_id'] }}">
              @if (!$is_me && isset($message['sender_name']))
                <div class="message-sender">
                  @if (!empty($message['sender_avatar']))
                    <img src="/BookHeaven2.0/{{ e($message['sender_avatar']) }}" alt="{{ e($message['sender_name']) }}" class="message-sender-avatar">
                  @else
                    <div class="message-sender-avatar"
                         style="background-color:#{{ substr(md5($message['sender_name']),0,6) }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:.7rem;">
                      {{ substr($message['sender_name'],0,2) }}
                    </div>
                  @endif
                </div>
              @endif
              <div class="message-content">{{ e($message['content'] ?? '') }}</div>
              <div class="message-time">{{ $message['time_for_bubble'] ?? '' }}</div>
            </div>
          @endforeach
        </div>

        <form method="POST" class="message-input-container" id="messageForm" action="{{ route('community.messages.send', ['c_id'=>$community_id,'u_id'=>$current_recipient['user_id']]) }}">
          @csrf
          <input type="text" name="message" class="message-input" placeholder="Type a message..." required>
          <button type="submit" class="send-btn"><i class="fas fa-paper-plane"></i></button>
        </form>
      </div>
    @else
      <div class="chat-container" style="display:flex;align-items:center;justify-content:center;">
        <div style="text-align:center;padding:20px;">
          <i class="fas fa-comments" style="font-size:3rem;color:var(--text-light);margin-bottom:15px;"></i>
          <h3>Select a member to start chatting</h3>
          <p>Choose someone from the member list to begin your conversation</p>
        </div>
      </div>
    @endif
  </main>

  @include('partials.footer')

  <script>
    function scrollToBottom(){
      const c = document.getElementById('messagesContainer');
      if(c){ c.scrollTop = c.scrollHeight; }
    }
    document.addEventListener('DOMContentLoaded', function(){
      scrollToBottom();
      checkForUnreadCounts();
      checkForNewMessages();
    });

    const messageForm = document.getElementById('messageForm');
    if(messageForm){
      messageForm.addEventListener('submit', function(e){
        e.preventDefault();
        const input = this.querySelector('.message-input');
        const txt = input.value.trim();
        if(!txt) return;
        const fd = new FormData(this); fd.append('ajax', 'true');

        fetch(this.action, { method:'POST', body: fd })
          .then(r=>r.json())
          .then(d=>{
            if(d.status === 'success'){
              input.value = '';
              checkForUnreadCounts();
              checkForNewMessages();
            }
          }).catch(console.error);
      });
    }

    function checkForNewMessages(){
      const c = document.getElementById('messagesContainer');
      if(!c) return;

      const last = c.querySelector('.message:last-child');
      const lastId = last ? parseInt(last.dataset.messageId) : 0;

      const params = new URLSearchParams(window.location.search);
      const communityId = params.get('c_id');
      const recipientId = params.get('u_id');
      if(!communityId || !recipientId) return;

      const url = `{{ route('community.messages.ajax-get') }}?c_id=${communityId}&u_id=${recipientId}&last_id=${lastId}`;
      fetch(url)
        .then(r=>r.json())
        .then(messages=>{
          if(messages.length>0){
            messages.forEach(m=>{
              const isMe = (parseInt(m.sender_id) === parseInt({{ (int)$user_id }}));
              const t = m.time_for_bubble ?? '';
              let html = `<div class="message ${isMe?'message-me':'message-them'}" data-message-id="${m.message_id}">`;
              if(!isMe){
                let avatarHtml = '';
                if(m.sender_avatar){
                  avatarHtml = `<img src="/BookHeaven2.0/${m.sender_avatar}" alt="${m.sender_name}" class="message-sender-avatar">`;
                }else{
                  const bg = '#' + md5(m.sender_name).substr(0,6);
                  avatarHtml = `<div class="message-sender-avatar" style="background-color:${bg};display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:.7rem;">${(m.sender_name||'??').substr(0,2)}</div>`;
                }
                html += `<div class="message-sender">${avatarHtml}</div>`;
              }
              html += `<div class="message-content">${escapeHtml(m.content||'')}</div><div class="message-time">${t}</div></div>`;
              c.insertAdjacentHTML('beforeend', html);
            });
            scrollToBottom();
            checkForUnreadCounts();
          }
          setTimeout(checkForNewMessages, 6000);
        })
        .catch(err=>{ console.error(err); setTimeout(checkForNewMessages, 6000); });
    }

    function checkForUnreadCounts(){
      const params = new URLSearchParams(window.location.search);
      const cId = params.get('c_id');
      if(!cId) return;

      fetch(`{{ route('community.messages.ajax-unread') }}?c_id=${cId}`)
        .then(r=>r.json())
        .then(counts=>{
          counts.forEach(count=>{
            const el = document.getElementById(`unreadCount-${count.user_id}`);
            if(count.unread_count > 0){
              if(!el){
                const item = document.querySelector(`.member-item[data-user-id="${count.user_id}"]`);
                if(item){
                  item.insertAdjacentHTML('beforeend', `<div class="unread-count" id="unreadCount-${count.user_id}">${count.unread_count}</div>`);
                }
              }else{
                el.textContent = count.unread_count;
              }
            }else if(el){ el.remove(); }
          });
          setTimeout(checkForUnreadCounts, 5000);
        })
        .catch(err=>{ console.error(err); setTimeout(checkForUnreadCounts, 2000); });
    }

    function escapeHtml(text){
      const d = document.createElement('div'); d.textContent = text; return d.innerHTML;
    }

    function md5(string){
      let hash=0; for(let i=0;i<string.length;i++){const c=string.charCodeAt(i);hash=((hash<<5)-hash)+c;hash|=0;} return Math.abs(hash).toString(16).substr(0,6);
    }
  </script>
</body>
</html>

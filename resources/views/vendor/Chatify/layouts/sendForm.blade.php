@php
    use Illuminate\Support\Facades\Auth;
    
    $user = Auth::user();
    if($channel){
      $isOwner = ($channel->owner_id == $user->id) ?? true;
    
      $isChatEnable = ($channel->is_chat_disable == 0) ?? true;
    
      $isDisabled = $isChatEnable || $isOwner;
    
      $placeHolder = $isChatEnable ? 'Type a message..' : 'Only owner can send message';
    }
@endphp

<div class="messenger-sendCard">
    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf

        @if($channel)
            @if ($isChatEnable || $isOwner)
                <label><span class="fas fa-plus-circle"></span><input type="file" class="upload-attachment" name="file"
                        accept=".{{ implode(', .', config('chatify.attachments.allowed_images')) }}, .{{ implode(', .', config('chatify.attachments.allowed_files')) }}" /></label>
                <button class="emoji-button"><span class="fas fa-smile"></span></button>
            @endif
    
            <textarea {{ $isDisabled ? '' : 'disabled' }} name="message" class="m-send app-scroll" placeholder="{{ $placeHolder }}"></textarea>
    
            @if ($isChatEnable || $isOwner)
                <button class="send-button"><span
                        class="fas fa-paper-plane"></span></button>
            @endif
        @else
                <label><span class="fas fa-plus-circle"></span><input type="file" class="upload-attachment" name="file"
                        accept=".{{ implode(', .', config('chatify.attachments.allowed_images')) }}, .{{ implode(', .', config('chatify.attachments.allowed_files')) }}" /></label>
                <button class="emoji-button"><span class="fas fa-smile"></span></button>
                <textarea name="message" class="m-send app-scroll" placeholder="Type a message.."></textarea>
                <button class="send-button"><span
                        class="fas fa-paper-plane"></span></button>
        @endif


    </form>
</div>

<?php
$isGroup = isset($channel->owner_id);
?>
<nav>
    <p>{{ isset($channel->owner_id) ? 'Group Details' : 'User Details' }}</p>
    <a href="#"><i class="fas fa-times"></i></a>
</nav>

<div class="avatar avatar-channel av-l chatify-d-flex"></div>
<p class="info-name">{{ config('chatify.name') }}</p>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

@if ($isGroup)
    <div style="max-width: 250px; margin: auto">
        @if ($isGroup && $channel && $channel->owner_id === Auth::user()->id)
            <form id="update-owner-form">
                @csrf
                <select name="is_chat_disable" class="form-control" id="is_chat_disable" style="margin-bottom: 0px">
                    <option value="0" {{ $channel->is_chat_disable == 0 ? 'selected' : '' }}>All Members</option>
                    <option value="1" {{ $channel->is_chat_disable == 1 ? 'selected' : '' }}>Only Owner</option>
                </select>
                <button type="button" id="update-owner-btn" class="btn btn-primary">Save</button>
            </form>
        @endif
        <script>
            // var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $(document).ready(function() {
                $('#update-owner-btn').click(function() {
                    // Ambil channel_id dari url
                    const urlPath = window.location.pathname;

                    const parts = urlPath.split("/");
                    const channelId = parts[parts.length - 1];

                    var formData = $('#update-owner-form').serialize(); // Mengambil data form
                    const partsForm = formData.split('&');

                    const parsedData = {};

                    // Melakukan iterasi terhadap setiap pasangan parameter
                    partsForm.forEach(function(parameter) {
                        // Memecah pasangan parameter kunci-nilai menjadi kunci dan nilai
                        var parts = parameter.split("=");
                        // Menyimpan kunci dan nilainya dalam objek hasil parsing
                        parsedData[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
                    });

                    // Mendapatkan nilai is_chat_disable
                    var isChatDisableValue = parsedData["is_chat_disable"];
                    var tokenValue = parsedData["_token"];

                    $.ajax({
                        type: 'POST',
                        url: '{{ route('update.owner') }}', // URL untuk mengirim permintaan POST
                        data: {
                            _token: tokenValue,
                            channel_id: channelId,
                            is_chat_disable: isChatDisableValue
                        },
                        success: function(response) {
                            // Anda dapat menambahkan logika lain di sini, misalnya menampilkan pesan sukses atau melakukan sesuatu setelah berhasil
                            // console.log(response); // Menampilkan respon dari server
                            // Redirect back to previous page
                            window.location.href = document.referrer;
                        },

                        error: function(xhr, status, error) {
                            // Menampilkan respon dari server
                            console.error(xhr
                                .responseText); // Menampilkan pesan error jika permintaan gagal
                        }
                    });
                });
            });
        </script>



        <h4 style="text-align: center; margin-bottom: 10px; margin-top: 30px; font-weight: normal; font-size: 14px">
            Users
            in this group</h4>
        {{-- button add member grup --}}

        @if ($isGroup && $channel && $channel->owner_id === Auth::user()->id)
            <a href="#" class="add-member-btn"><i class="fas fa-user-plus" style="margin-bottom: 20px"></i></a>
        @endif
        
        
        <!--AKSI TAMBAH USER BARU PADA GRUP-->
        <script>
            // button show modal to add members
            $("body").on("click", ".add-member-btn", function (e) {
                e.preventDefault();
                app_modal({
                    show: true,
                    name: "addMember",
                });
            });
            
            
            const modalAddMember = $(".app-modal[data-name=addMember]");
            const memberSearchEl = modalAddMember.find(".user-search");
            const searchRecordsElMember = modalAddMember.find(".search-records");
            
            // button close modal to add members [cancel button]
            modalAddMember.find(".app-modal-footer .cancel").on("click", function () {
                app_modal({
                    show: false,
                    name: "addMember",
                });
            });
            
            // search user member
            function handleMemberSearch(input) {
                const modalAddMember = $(".app-modal[data-name=addMember]");
                const searchRecordsElMember = modalAddMember.find(
                    ".search-records.add-member"
                );
                const addedUserIds = []; // Anda perlu menyesuaikan ini sesuai dengan data pengguna yang sudah ditambahkan ke grup
        
                // Logika pencarian pengguna
                $.ajax({
                    url: url + "/search-users", // Sesuaikan dengan URL yang benar untuk pencarian pengguna
                    method: "GET",
                    data: { _token: csrfToken, input: input },
                    dataType: "JSON",
                    success: (data) => {
                        let html = "";
                        if (typeof data.records == "string") {
                            html = data.records;
                        } else {
                            data.records
                                .filter(({ user }) => !addedUserIds.includes(user.id))
                                .forEach(({ user, view }) => {
                                    html += view;
                                });
                        }
                        searchRecordsElMember.html(html);
                        // update data-action required with [responsive design]
                        cssMediaQueries();
                    },
                    error: (error) => {
                        console.error(error);
                    },
                });
            }
            
            $(document).ready(function () {
                // Inisialisasi elemen pencarian pengguna pada modal "Add Member"
                const modalAddMember = $(".app-modal[data-name=addMember]");
                const memberSearchElMember = modalAddMember.find(".user-search");
        
                // event handler untuk pencarian pengguna pada modal "Add Member"
                const debouncedMemberSearch = debounce(function () {
                    const value = memberSearchElMember.val();
                    handleMemberSearch(value); // Memanggil fungsi pencarian pengguna pada modal Add Member
                }, 500);
        
                memberSearchElMember.on("keyup", function (e) {
                    const value = $(this).val();
                    if ($.trim(value).length > 0) {
                        memberSearchElMember.trigger("focus");
                        debouncedMemberSearch();
                    }
                });
            });
            
            // 1. Tambahkan Input Tersembunyi untuk channel_id saat grup diklik
            $("body").on(
                "click",
                ".search-records.add-member .user-list-item",
                function () {
                    const userID = $(this).attr("data-user");
                    const channelID = $(this).attr("data-channel"); // Ambil channel_id dari data atribut
                    const addedUserView = modalAddMember.find(".selected-users");
        
                    addedUserView.prepend($(this));
                    addedUserIds.push(Number(userID));
        
                    // Setel channel_id ke dalam input tersembunyi
                    $("#channel_id").val(channelID);
                }
            );
            
            // 2. Atur user_id saat pengguna dipilih dari hasil pencarian
            $("body").on(
                "click",
                ".search-records.add-member .user-list-item",
                function () {
                    const userID = $(this).attr("data-user");
                    console.log(userID);
        
                    // Tambahkan user_id ke dalam input tersembunyi
                    const hiddenInput = `<input type="hidden" name="user_ids[]" value="${userID}">`;
                    $("#addMemberForm").append(hiddenInput);
                }
            );
            
            /* -------- Add Member Modal -------- */
            $("#addMemberForm").on("submit", (e) => {
                e.preventDefault();
                addMembersToGroup(); // Panggil fungsi untuk menambahkan anggota ke grup
            });
        
            // post add member grup
            function addMembersToGroup() {
                const addMemberForm = $("#addMemberForm");
                const formData = new FormData(addMemberForm[0]);
        
                // Ambil channel_id dari url
                const urlPath = window.location.pathname;
        
                const parts = urlPath.split("/");
                const channelId = parts[parts.length - 1];
        
                // Ambil user_id dari daftar yang dipilih
                const selectedUsers = modalAddMember.find(
                    ".selected-users .user-list-item"
                );
        
                selectedUsers.each(function () {
                    formData.append("user_id[]", $(this).attr("data-user")); // Ganti 'user_id[]' sesuai dengan key yang benar
                });
        
                // Tambahkan channel_id ke formData
                formData.append("channel_id", channelId);
        
                $.ajax({
                    // url: 'http://localhost:8000/member',
                    url: addMemberForm.attr("action"),
                    method: "POST",
                    data: formData,
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        app_modal({
                            show: false,
                            name: "addMember",
                        });
                        app_modal({
                            show: true,
                            name: "alert",
                            buttons: false,
                            body: loadingSVG("32px", null, "margin:auto"),
                        });
                    },
                    success: (data) => {
                        if (data.error) {
                            app_modal({
                                show: true,
                                name: "alert",
                                buttons: true,
                                body: data.msg,
                            });
                        } else {
                            app_modal({
                                show: false,
                                name: "alert",
                                buttons: true,
                                body: "",
                            });
        
                            addMemberForm.trigger("reset");
                            modalAddMember.find(".selected-users").html("");
                            modalAddMember.find(".search-records").html("");
                        }
        
                        // Redirect back to previous page
                        window.location.href = document.referrer;
                    },
        
                    error: (xhr, status, error) => {
                        console.error("XHR request failed:", status, error, xhr);
                    },
                });
            }
        </script>
    
        <div class="app-scroll users-list " style="overflow-y: scroll; max-height: 100px;">
            @foreach ($channel->users as $user)
                <div class="flex user-item" style="display: flex; align-items: center">
                    {!! view('Chatify::layouts.listItem', [
                        'get' => 'user_search_item',
                        'user' => Chatify::getUserWithAvatar($user),
                    ])->render() !!}

                    {{-- button delete member from group --}}
                    @if ($isGroup && $channel && $channel->owner_id === Auth::user()->id)
                        <a href="#" class="delete-member-btn" data-user-id="{{ $user->id }}"><i
                                class="fas fa-user-minus"></i></a>
                    @endif
                </div>
            @endforeach
            
            <script>
                // button show modal to add members
                $("body").on("click", ".delete-member-btn", function (e) {
                    e.preventDefault();
                    var userId = $(this).data("user-id"); // Ambil user_id dari data atribut tombol
                    $("#deleteUserId").val(userId); // Set nilai user_id pada input tersembunyi di dalam modal
                    app_modal({
                        show: true,
                        name: "deleteMember",
                    });
                });
                
                // delete modal [cancel button]
                $(".app-modal[data-name=deleteMember]")
                    .find(".app-modal-footer .cancel")
                    .on("click", function () {
                        app_modal({
                            show: false,
                            name: "deleteMember",
                        });
                    });
            </script>


        </div>

    </div>
@endif

<div class="messenger-infoView-btns">
    @if ($isGroup && $channel && $channel->owner_id === Auth::user()->id)
        <a href="#" class="danger delete-group">Delete Group</a>
    @elseif($isGroup)
        <a href="#" class="danger leave-group">Leave Group</a>
    @else
        {{-- <a href="#" class="danger delete-conversation">Delete Conversation</a> --}}
    @endif
</div>

{{-- shared photos --}}
<div class="messenger-infoView-shared">
    <p class="messenger-title"><span>Shared Photos</span></p>
    <div class="shared-photos-list"></div>
</div>



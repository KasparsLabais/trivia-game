<script>
    const playerJoined = (data) => {
        console.log("playerJoined ",data);
        //alert(data.message);
        let playerHolder = document.getElementById('player-holder');

        let playerDiv = document.createElement('div');
        playerDiv.classList.add('flex', 'flex-row', 'py-2');

        let playerAvatarImg = document.createElement('img');
        playerAvatarImg.classList.add('w-14','h-14','rounded-full','shadow-md','border-2','border-slate-500');
        playerAvatarImg.setAttribute('src', data.player.avatar);

        let playerInfoDiv = document.createElement('div');
        playerInfoDiv.classList.add('flex', 'flex-col', 'px-2');

        let playerUsername = document.createElement('div');
        playerUsername.classList.add('raleway');
        playerUsername.innerHTML = data.player.username;

        let playerStatus = document.createElement('div');
        playerStatus.classList.add('raleway');
        if (data.player.id == data.gameInstance.user_id){
            playerStatus.innerHTML = 'Host';
        } else {
            playerStatus.innerHTML = 'Player';
        }

        playerInfoDiv.append(playerUsername);
        playerInfoDiv.append(playerStatus);

        playerDiv.appendChild(playerAvatarImg);
        playerDiv.appendChild(playerInfoDiv);

        playerHolder.appendChild(playerDiv);
    }
</script>
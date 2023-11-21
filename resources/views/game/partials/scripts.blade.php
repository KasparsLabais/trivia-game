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

    const playerJoinedUserView = (data) =>
    {
        console.log("playerJoinedUserView ",data);

        let playerHolder = document.getElementById('player-holder');

        let playerDiv = document.createElement('div');
        playerDiv.classList.add('flex', 'flex-row', 'py-2', 'px-2', 'w-1/3');

        let playerInfoDiv = document.createElement('div');
        playerInfoDiv.classList.add('flex', 'flex-col', 'bg-zinc-700', 'rounded-md', 'shadow-zinc-700');

        let playerAvatarDiv = document.createElement('div');
        playerAvatarDiv.classList.add('flex', 'flex-row', 'w-full', 'justify-center', 'relative', 'bg-gray-600', 'h-16', 'bg-cover', 'bg-center', 'bg-no-repeat');
        playerAvatarDiv.style.backgroundImage = 'url("' + data.player.avatar + '")';

        let playerUsernameDiv = document.createElement('div');
        playerUsernameDiv.classList.add('flex', 'flex-col', 'px-2', 'py-2');

        let playerUsername = document.createElement('div');
        playerUsername.classList.add('raleway', 'text-slate-200', 'font-bold');
        playerUsername.innerHTML = data.player.username;

        playerUsernameDiv.appendChild(playerUsername);

        playerInfoDiv.appendChild(playerAvatarDiv);
        playerInfoDiv.appendChild(playerUsernameDiv);

        playerDiv.appendChild(playerInfoDiv);
        playerHolder.appendChild(playerDiv);
    }
</script>
<script>
    const playerJoined = (data) => {
        console.log("playerJoined ",data);
        //alert(data.message);
        let playerHolder = document.getElementById('player-holder');

        let playerDiv = document.createElement('div');
        playerDiv.classList.add('flex', 'flex-row', 'justify-between');

        let playerUsername = document.createElement('div');
        playerUsername.innerHTML = data.player.username;

        let playerStatus = document.createElement('div');
        if (data.player.id == data.gameInstance.user_id){
            playerStatus.innerHTML = 'Host';
        } else {
            playerStatus.innerHTML = 'Player';
        }

        playerDiv.appendChild(playerUsername);
        playerDiv.appendChild(playerStatus);

        playerHolder.appendChild(playerDiv);
    }
</script>
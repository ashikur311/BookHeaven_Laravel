@extends('layouts.app')
@section('title', 'Audiobook Player')

@section('content')
<link rel="stylesheet" href="{{ asset('css/music-player.css') }}">

<main class="music-wrapper">
    <aside>
        <h2>Your Audiobooks</h2>
        <ul class="song-list" id="songList">
            <div class="no-books" id="noBooksMessage">Loading your audiobooks...</div>
        </ul>
    </aside>

    <div class="player-container">
        <div class="background-art" id="backgroundArt"></div>
        <div class="now-playing">
            <img src="{{ url('assets/default-audiobook-cover.png') }}" alt="Audiobook Cover" class="album-art" id="albumArt">

            <div class="song-details">
                <h1 class="song-name" id="songName">No Audiobook Selected</h1>
                <p class="artist-name" id="artistName">Select an audiobook from the list</p>
            </div>

            <div class="progress-container">
                <div class="progress-bar" id="progressBar">
                    <div class="progress" id="progress"></div>
                </div>
                <div class="time-info">
                    <span class="current-time" id="currentTime">0:00</span>
                    <span class="duration" id="duration">0:00</span>
                </div>
            </div>

            <div class="controls">
                <button class="control-btn" id="prevBtn">⏮</button>
                <button class="control-btn play-btn" id="playBtn">▶</button>
                <button class="control-btn" id="nextBtn">⏭</button>
            </div>

            <div class="volume-control">
                <span>🔈</span>
                <div class="volume-slider" id="volumeSlider">
                    <div class="volume-progress" id="volumeProgress"></div>
                </div>
                <span>🔊</span>
            </div>
        </div>
    </div>
</main>

<audio id="audioPlayer"></audio>

<script>
    const audioPlayer = document.getElementById('audioPlayer');
    const songList = document.getElementById('songList');
    const playBtn = document.getElementById('playBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const progressBar = document.getElementById('progressBar');
    const progress = document.getElementById('progress');
    const currentTimeEl = document.getElementById('currentTime');
    const durationEl = document.getElementById('duration');
    const volumeSlider = document.getElementById('volumeSlider');
    const volumeProgress = document.getElementById('volumeProgress');
    const albumArt = document.getElementById('albumArt');
    const songName = document.getElementById('songName');
    const artistName = document.getElementById('artistName');
    const backgroundArt = document.getElementById('backgroundArt');
    const noBooksMessage = document.getElementById('noBooksMessage');

    let isPlaying = false;
    let currentSongIndex = 0;
    let audiobooks = [];

    document.addEventListener('DOMContentLoaded', async () => {
        await fetchUserAudiobooks();
        if (audiobooks.length > 0) loadSong(currentSongIndex);
        setupPlayer();
    });

    async function fetchUserAudiobooks() {
        try {
            const response = await fetch(`{{ route('audiobooks.user') }}`);
            const data = await response.json();

            if (data.success && data.audiobooks.length > 0) {
                audiobooks = data.audiobooks;
                renderAudiobookList();
            } else {
                noBooksMessage.textContent = "You don't have any audiobooks.";
            }
        } catch {
            noBooksMessage.textContent = "Error loading audiobooks.";
        }
    }

    function renderAudiobookList() {
        songList.innerHTML = '';
        audiobooks.forEach((a, i) => {
            const li = document.createElement('li');
            li.className = `song-item ${i === currentSongIndex ? 'active' : ''}`;
            li.dataset.index = i;
            li.innerHTML = `
                <img src="${a.poster_url}" class="song-cover">
                <div class="song-info">
                    <div class="song-title">${a.title}</div>
                    <div class="song-artist">${a.writer}</div>
                </div>`;
            li.onclick = () => { currentSongIndex = i; loadSong(i); if (!isPlaying) togglePlay(); };
            songList.appendChild(li);
        });
    }

    function loadSong(index) {
        const b = audiobooks[index];
        albumArt.src = b.poster_url;
        songName.textContent = b.title;
        artistName.textContent = b.writer;
        backgroundArt.style.backgroundImage = `url(${b.poster_url})`;
        audioPlayer.src = b.audio_url;
        document.querySelectorAll('.song-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.song-item')[index].classList.add('active');
    }

    function setupPlayer() {
        playBtn.onclick = togglePlay;
        prevBtn.onclick = prevSong;
        nextBtn.onclick = nextSong;
        progressBar.onclick = e => {
            const width = progressBar.clientWidth;
            const clickX = e.offsetX;
            audioPlayer.currentTime = (clickX / width) * audioPlayer.duration;
        };
        volumeSlider.onclick = e => {
            const width = volumeSlider.clientWidth;
            const clickX = e.offsetX;
            const v = clickX / width;
            audioPlayer.volume = v;
            volumeProgress.style.width = `${v * 100}%`;
        };
        audioPlayer.addEventListener('timeupdate', updateProgress);
        audioPlayer.addEventListener('ended', nextSong);
        audioPlayer.addEventListener('loadedmetadata', () => durationEl.textContent = formatTime(audioPlayer.duration));
    }

    function togglePlay() {
        if (!audiobooks.length) return;
        if (isPlaying) {
            audioPlayer.pause(); playBtn.textContent = '▶';
        } else {
            audioPlayer.play(); playBtn.textContent = '⏸';
        }
        isPlaying = !isPlaying;
    }

    function prevSong() {
        if (!audiobooks.length) return;
        currentSongIndex = (currentSongIndex - 1 + audiobooks.length) % audiobooks.length;
        loadSong(currentSongIndex);
        if (isPlaying) audioPlayer.play();
    }

    function nextSong() {
        if (!audiobooks.length) return;
        currentSongIndex = (currentSongIndex + 1) % audiobooks.length;
        loadSong(currentSongIndex);
        if (isPlaying) audioPlayer.play();
    }

    function updateProgress() {
        const { currentTime, duration } = audioPlayer;
        progress.style.width = `${(currentTime / duration) * 100}%`;
        currentTimeEl.textContent = formatTime(currentTime);
    }

    const formatTime = s => isNaN(s) ? '0:00' : `${Math.floor(s / 60)}:${String(Math.floor(s % 60)).padStart(2, '0')}`;
</script>
@endsection

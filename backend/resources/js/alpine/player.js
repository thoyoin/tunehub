document.addEventListener('alpine:init', () => {
    Alpine.store('player',{
        audio: null,
        currentTrack: null,
        queue: [],
        currentIndex: null,
        isPlaying: false,
        progress: 0,
        currentTime: 0,
        duration: 0,
        volume: 0.3,
        isMuted: false,

        init(audioEl) {
            this.audio = audioEl
            this.audio.volume = this.volume

            const lastPlayed = localStorage.getItem('lastPlayed')

            if (lastPlayed) {
                this.currentTrack = JSON.parse(localStorage.getItem('lastPlayed'))
                this.audio.src = this.currentTrack.audio_url
            }

            audioEl.addEventListener('timeupdate', () => {
                this.progress = (audioEl.currentTime / audioEl.duration) * 100;
                this.currentTime = audioEl.currentTime
            })

            audioEl.addEventListener('loadedmetadata', () => {
                this.duration = audioEl.duration
            })

            audioEl.addEventListener('ended', () => {
                this.next()
            })
        },

        playTrack(track, queue = []) {
            this.queue = queue
            this.currentIndex = queue.findIndex(t => t.id === track.id)
            this.audio.src = track.audio_url
            this.audio.play()
            this.currentTrack = track
            this.isPlaying = true

            localStorage.setItem('lastPlayed', JSON.stringify(track))
        },

        toggle() {
            if (!this.audio) return

            if (this.audio.paused) {
                this.audio.play()
                this.isPlaying = true
            } else {
                this.audio.pause()
                this.isPlaying = false
            }
        },

        toggleVolume() {
            if (this.audio.volume > 0) {
                this.audio.volume = 0
                this.isMuted = true
            } else {
                this.audio.volume = this.volume
                this.isMuted = false
            }
        },

        toggleTrack(track, queue = []) {
            if (this.currentTrack?.id === track.id) {
                if (this.audio.paused) {
                    this.audio.play()
                    this.isPlaying = true
                } else {
                    this.audio.pause()
                    this.isPlaying = false
                }
                return
            }
            this.playTrack(track, queue)
        },

        next() {
            if (this.currentIndex === null) return
            if (this.currentIndex + 1 >= this.queue.length) return

            this.currentIndex++
            this.playTrack(this.queue[this.currentIndex], this.queue)
        },

        prev() {
            if (this.currentIndex === null) return
            if (this.currentIndex === 0) return

            this.currentIndex--
            this.playTrack(this.queue[this.currentIndex], this.queue)
        },

        seek(e) {
            const width = e.currentTarget.clientWidth;
            const clickX = e.offsetX;
            this.audio.currentTime = (clickX / width) * this.duration;
        },

        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60)
            const secs = Math.floor(seconds % 60)

            return `${minutes}:${secs.toString().padStart(2, '0')}`
        },

        setVolume(value) {
            this.volume = value
            this.audio.volume = value
        },
    })
})

<script setup>
import { useAudioPlayer } from "@/composables/useAudioPlayer.js";
import { useAuthStore } from "@/stores/auth.js";
import { ref } from "vue";

const audioRef = ref(null);
const auth = useAuthStore();

const {prev, toggleTrack, volume, next, toggle,
    isPlaying, currentTrack, formatTime, currentTime,
    seek, progress, toggleVolume, isMuted, setVolume,
} = useAudioPlayer(audioRef);

</script>

<template>
    <audio ref="audioRef"></audio>
<div
    style="max-height: 85px; bottom: 10px;"
    class="d-flex z-3 position-fixed flex-row py-2 h-100 w-100 justify-content-center align-items-center"
>
    <div
        style="border-bottom:1px solid rgba(228, 228, 228, 0.15);border-top:1px solid rgba(228, 228, 228, 0.15)"
        class="d-flex bg-player rounded-5 flex-row py-4 h-100 w-75"
    >
        <template v-if="!auth.user">
            <div class="fw-bold w-100 text-center" style="color: rgb(228,228,228)">
                Sign up to listen to unlimited music!
            </div>
        </template>
        <template v-else>
            <div class="d-flex flex-row align-items-center justify-content-between px-4 w-100">
                <div class="d-flex flex-row gap-2 ms-3 align-items-center">
                    <button @click="prev" class="btn btn-play">
                        <img src="@/assets/svg/prev.svg" alt="prev">
                    </button>
                    <button @click="toggle" class="btn btn-play">
                    <span v-if="!isPlaying">
                        <img src="@/assets/svg/play.svg" alt="play">
                    </span>
                    <span v-if="isPlaying">
                        <img src="@/assets/svg/pause.svg" alt="pause">
                    </span>
                    </button>
                    <button @click="next" class="btn btn-play">
                        <img src="@/assets/svg/next.svg" alt="next">
                    </button>
                </div>
                <div class="d-flex flex-column align-items-start mt-1 w-100" style="max-width: 350px">
                    <div class="d-flex flex-row my-1">
                        <img
                            :src="currentTrack?.cover_url"
                            style="width: 35px; height: 35px"
                            class="rounded-3"
                            alt="cover"
                        >
                        <div class="d-flex flex-column ms-2">
                            <span style="color: rgb(228,228,228);font-size:12px;font-weight: bold" v-text="currentTrack?.title"></span>
                            <span style="color: rgb(228,228,228);font-size:12px;opacity: 50%" v-text="currentTrack?.artist"></span>
                        </div>
                    </div>
                    <div class="d-flex flex-row w-100 align-items-center justify-content-between">
                        <span
                            v-text="formatTime(currentTime)"
                            style="color: rgb(228,228,228);font-size:12px;opacity: 50%"
                            class="d-flex me-2"
                        ></span>
                        <div
                            style="max-width: 260px"
                            class="progress-container w-100"
                            @click="seek"
                        >
                            <div class="progress" :style="`width: ${progress}%`"></div>
                        </div>
                        <span
                            style="color: rgb(228,228,228);font-size:12px;opacity: 50%"
                            class="d-flex ms-2"
                            v-text="currentTrack?.formatted_duration"
                        ></span>
                    </div>
                </div>
                <div class="d-flex align-items-center volume-bg">
                    <button class="btn btn-play pe-1" @click="toggleVolume">
                    <span v-if="!isMuted">
                        <img src="@/assets/svg/volume.svg" alt="volume">
                    </span>
                    <span v-if="isMuted">
                        <img src="@/assets/svg/volume-muted.svg" alt="volume">
                    </span>
                    </button>
                    <input
                        class="volume"
                        type="range"
                        min="0"
                        max="1"
                        step="0.01"
                        :style="`--volume-percent: ${volume * 100}%`"
                        :value="volume"
                        @input="setVolume($event.target.value)"
                    >
                </div>
            </div>
        </template>
    </div>
</div>
</template>

<style scoped>
.bg-player {
    background-color: rgba(50,50,51, 15%) !important;
    backdrop-filter: blur(8px) !important;
}
.progress-container {
    background-color: rgb(32,32,32) !important;
    border-radius: 5px !important;
    width: 100%;
    height: 4px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: .3s !important;
    &:hover {
        height: 10px !important;
        .progress {
            height: 10px !important;
        }
    }
    .progress {
        border-radius: 5px !important;
        background: rgba(228,228,228, 50%);
        height: 4px;
        width: 40%;
        cursor: pointer;
        transition: .3s !important;
    }
}
.volume {
    -webkit-appearance: none;
    transition: .3s !important;
    appearance: none;
    width: 90px;
    height: 5px;
    border-radius: 5px;
    cursor: pointer;
    background: linear-gradient(
        to right,
        rgba(228,228,228, 50%) 0%,
        rgba(228,228,228, 50%) var(--volume-percent),
        rgb(35,35,35) var(--volume-percent),
        rgb(45,45,45) 100%
    );
    &:hover {
        height: 10px !important;
    }
}

.volume::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 0;
    height: 0;
    cursor: pointer;
}

.volume::-moz-range-thumb {
    width: 12px;
    height: 12px;
    background: rgb(228,228,228);
    border-radius: 50%;
    border: none;
    cursor: pointer;
}

.volume::-moz-range-track {
    height: 5px;
    background-color: rgb(32,32,32);
    border-radius: 5px;
}

.volume-bg {
    border-radius: 15px;
    padding: 0 5px;
}

</style>

<script setup lang="ts">
import AudioPlayer from "@/components/AudioPlayer.vue";

</script>

<template>
    <div id="app">
        <router-view/>
        <AudioPlayer/>
    </div>
</template>

<style lang="scss">
    .app-wrapper {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 100% !important;
        min-height: 0 !important;
    }
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.1s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
    .bg-minor {
        background-color: rgba(32,32,32, 50%) !important;
        backdrop-filter: blur(10px) !important;
        &::placeholder {
            color: rgba(228,228,228, .5) !important;
        }
    }
    .bg-major {
        background-color: rgb(32,32,32) !important;
    }
    .form-control {
        &:focus {
            box-shadow: none !important;
            border-color: rgb(158, 23, 63) !important;
        }
    }
    .btn-primary {
        background: #bd1045 !important;
        border: 1px solid rgba(4, 4, 214, 0) !important;
        border-radius: 15px !important;
        color: rgb(228,228,228) !important;
        height: 30px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 10px !important;
        transition: .2s !important;

        &:hover {
            background-color: #b31b47 !important;
            border-color: #4a0b1e !important;
        }

        &:active {
            background-color: #c11c4c !important;
            border-color: #c11c4c !important;
        }
    }
    .btn-cancel {
        background: rgb(32,32,32) !important;
        border: 1px solid rgba(4, 4, 214, 0) !important;
        border-radius: 15px !important;
        color: rgb(228,228,228) !important;
        height: 30px;
        display: flex;
        align-items: center;
        padding: 0 10px !important;

        &:hover {
            border-color: rgb(46, 46, 46) !important;
        }

        &:active {
            border-color: rgb(32,32,32) !important;
        }
    }
    .btn-artists {
        display: flex !important;
        align-items: center !important;
        background-color: rgba(32,32,32, 30%) !important;
        backdrop-filter: blur(10px) !important;
        height: 30px !important;
        border-radius: 15px !important;
        font-size: 15px !important;
        padding: 0 7px !important;
        color: #ff2667 !important;
        transition: .1s ease-in-out !important;

        &::placeholder {
            color: rgba(228,228,228, .5) !important;
        }

        &:hover {
            background-color: rgb(40,40,41) !important;
            color: #ff2667 !important;
        }
        &:active {
            border-color:  rgb(75,75,75) !important;
        }
    }
    .btn-add {
        padding: 0 5px 3px !important;
        color: #9e173f !important;
        &:hover {
            background-color: rgb(40,40,41) !important;
        }
        &:active {
            border-color:  rgb(75,75,75) !important;
        }
    }
    .btn-new {
        padding: 0;
        color: rgb(228,228,228) !important;
        &:hover {
            border-color:  rgb(75,75,75) !important;
        }
        &:active {
            background-color: rgb(50,50,51) !important;
        }
    }
    .btn-playlist {
        color: rgb(228,228,228) !important;
        background-color: rgba(30,30,31, 15%) !important;
        border-radius: 15px !important;

        &:hover {
            box-shadow: 0 0 5px 3px rgb(41, 41, 41) !important;
            .cover-play-btn {
                opacity: .9 !important;
            }
            .playlist-cover {
                opacity: .5 !important;
            }
        }
        &:active {
            background-color: rgb(40,40,41) !important;
            border-color:  rgb(75,75,75) !important;
        }
    }
    .add-like {
        padding: 0 !important;
        opacity: 0 !important;
        transition: .3s !important;
    }
    .btn-play {
        padding: 0 !important;
        transition: .2s !important;
        border: none !important;
        &:hover {
            opacity: .4 !important;
        }
        &:active {
            border: none !important;
        }
    }
    .btn-settings {
        border: none !important;
        &:active {
            border: none !important;
            opacity: 1 !important;
        }
        &:hover {
            opacity: .7 !important;
        }
    }
    .playlist-cover {
        width: 45px;
        height: 45px;
    }
    .drag-handle {
        cursor: pointer;
    }
    .dropdown-menu {
        background-color: rgba(40,40,41, .95) !important;
        backdrop-filter: blur(10px) !important;
        z-index: 10000 !important;
        border-radius: 12px !important;
        padding: 4px 4px !important;
        max-height: 400px !important;
        .dropdown-item {
            color: rgb(228,228,228) !important;
            cursor: pointer !important;
            display: flex !important;
            z-index: 10000 !important;
            border-radius: 10px !important;
            height: 27px !important;
            font-size: 15px !important;
            align-items: center !important;
            transition: .2s !important;
            &:hover {
                background: rgba(158, 23, 63, 1) !important;
                transition: .1s !important;
            }
        }
    }

    .activeLibraryItem {
        border-color: rgb(75,75,75) !important;
        box-shadow: 0 0 5px 3px rgb(32,32,32) !important;
    }

    .track-row {
        &:hover td {
            background-color: rgba(50,50,51, 50%) !important;
            cursor: default !important;
            transition: background-color .2s ease !important;
        }

        &:hover {
            .add-like {
                opacity: .7 !important;
            }
            .btn-play-table {
                opacity: 1 !important;
                z-index: 1 !important;
            }
            .position-number {
                opacity: 0 !important;
            }
            .playing-wave {
                opacity: 0 !important;
            }
        }

        &:hover td:first-child {
            border-top-left-radius: 10px !important;
            border-bottom-left-radius: 10px !important;
        }

        &:hover td:last-child {
            border-top-right-radius: 10px !important;
            border-bottom-right-radius: 10px !important;
        }
    }
    .position-number {
        opacity: .5 !important;
        transition: .3s !important;
    }
    .table {
        --bs-table-bg: rgb(32,32,32) !important;
        --bs-table-color: rgb(228,228,228) !important;
    }
    .modal-content {
        background: rgba(40,40,41, .5) !important;
        backdrop-filter: blur(5px) !important;
        color: rgb(228,228,228) !important;
        border: 1px solid rgba(228, 228, 228, 0.15) !important;
        border-radius: 35px !important;

        .modal-header {
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            .modal-title {
                font-weight: bold !important;
                font-size: 18px !important;
            }
        }
        .modal-footer {
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 90px !important;
        }
    }
    .release-content {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 !important;
        overflow-y: auto !important;
        padding: 0 0 100px 0 !important;
    }
    .btn-add-like {
        margin: 10px !important;
        padding: 0 !important;
        opacity: .8 !important;
        transition: .2s !important;
        border: none !important;
        &:hover {
            outline: 1.5px dashed rgb(158, 23, 63) !important;
            border-radius: 50% !important;
        }
        &:active {
            outline: 1.5px solid rgb(158, 23, 63) !important;
        }
    }
    .btn-play-table {
        opacity: 0 !important;
        padding: 0 !important;
        transition: .2s !important;
        border: none !important;
        z-index: 0 !important;

        &:hover {
            opacity: .4 !important;
        }
        &:active {
            border: none !important;
        }
    }
    html, body {
        height: 100% !important;
        margin: 0 !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    #app {
        display: flex;
        flex-direction: column;
        height: 100%;
        cursor: default;
    }

    * {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    *::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }
    .custom-popover {
        --bs-popover-max-width: 200px !important;
        --bs-popover-bg: rgb(40,40,41) !important;
        --bs-popover-body-color: rgb(228,228,228) !important;
        --bs-popover-border-radius: 20px !important;
    }

    .loading-overlay {
        position: absolute !important;
        inset: 0 !important;
        background: rgba(32, 32, 32, 0.5) !important;
        backdrop-filter: blur(4px) !important;
        border-radius: 18px !important;
        z-index: 1 !important;
        pointer-events: all !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .search-spinner {
        width: 18px !important;
        height: 18px !important;
        border: 2px solid rgba(228, 228, 228, 0.2) !important;
        border-top: 2px solid rgb(158, 23, 63) !important;
        border-radius: 50% !important;
        animation: spin 0.4s linear infinite !important;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .honeypot-field {
        position: absolute !important;
        left: -9999px !important;
    }
</style>

<script setup>
import AudioPlayer from "@/components/AudioPlayer.vue";

</script>

<template>
    <div id="app">
        <router-view/>
        <AudioPlayer/>
    </div>
</template>

<style lang="scss">
    .bg-minor {
        background-color: rgba(50,50,51, 15%) !important;
        backdrop-filter: blur(8px) !important;
        &::placeholder {
            color: rgba(228,228,228, .5) !important;
        }
    }
    .bg-major {
        background-color: rgb(32,32,32) !important;
    }
    .btn-primary {
        background-color: #ff2667 !important;
        border: 1px solid #ff2667 !important;
        color: rgb(228,228,228) !important;

        &:hover {
            background-color: #b31b47 !important;
            border-color: #4a0b1e !important;
            color: rgb(228,228,228) !important;
        }

        &:active {
            background-color: #c11c4c !important;
            border-color: #c11c4c !important;
        }
    }
    .btn-artists {
        background-color: rgba(32,32,32, 30%) !important;
        backdrop-filter: blur(10px) !important;

        &::placeholder {
            color: rgba(228,228,228, .5) !important;
        }

        padding: 0 5px 3px !important;
        color: #ff2667 !important;

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

        &:hover {
            box-shadow: 0 0 5px 3px rgb(41, 41, 41) !important;
        }
        &:active {
            background-color: rgb(40,40,41) !important;
        }
    }
    .add-like {
        padding: 0 !important;
        opacity: 0 !important;
        transition: .3s !important;
    }
    .btn-play {
        padding: 0 !important;
        transition: .3s !important;
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
        background-color: rgba(40,40,41, 0.8) !important;
        z-index: 10000 !important;
        .dropdown-item {
            color: rgb(228,228,228) !important;
            &:hover {
                background-color: rgba(30, 30, 31, 0.8) !important;
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
            cursor: pointer !important;
            transition: background-color .2s ease, box-shadow .15s ease !important;
        }

        &:hover {
            box-shadow: inset 0 0 0 1px rgb(60,60,61) !important;

            .add-like {
                opacity: .7 !important;
            }
            .btn-play-table {
                opacity: 1 !important;
            }
            .position-number {
                opacity: 0 !important;
            }
            .playing-wave {
                opacity: 0 !important;
            }
        }

        &:hover td:first-child {
            border-top-left-radius: 20px !important;
            border-bottom-left-radius: 20px !important;
        }

        &:hover td:last-child {
            border-top-right-radius: 20px !important;
            border-bottom-right-radius: 20px !important;
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
        background: rgb(40,40,41) !important;
        color: rgb(228,228,228) !important;
        .modal-header {
            border-color: rgb(75,75,75) !important;
        }
        .modal-footer {
            border-color: rgb(75,75,75) !important;
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
        transition: .3s !important;
        border: none !important;
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
    }
    #app {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .custom-popover {
        --bs-popover-max-width: 200px !important;
        --bs-popover-bg: rgb(40,40,41) !important;
        --bs-popover-body-color: rgb(228,228,228) !important;
        --bs-popover-border-radius: 20px !important;
    }
</style>

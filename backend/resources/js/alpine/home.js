document.addEventListener('alpine:init', () => {
    Alpine.data('homePage', () => ({
        selectedLibraryItem: null,
        release: null,
        editingPlaylist: null,
        coverPreview: null,
        picturePreview: null,
        user: window.appData?.user ?? null,
        isReleaseLiked: null,
        libraryItem: null,
        itemTracks: null,

        async getRelease(release) {
            const response = await fetch(`/release/${release}`, {
                method: 'GET',
            })

            const data = await response.json();
            this.release = data[0];
            this.isReleaseLiked = data[2];
        },
        async getPlaylist(playlist) {
            const response = await fetch(`/playlists/${playlist}`, {
                method: 'GET',
            })

            const data = await response.json();
            this.libraryItem = data.playlist;
            this.itemTracks = data.tracks;
        },
        previewCover (e) {
            const file = event.target.files[0];
            if (!file) return;

            this.coverPreview = URL.createObjectURL(file);
        },
        previewPicture (e) {
            const file = e.target.files[0]
            if (!file) return

            this.picturePreview = URL.createObjectURL(file)
        },
        async getLibraryItem(item) {
            const response = await fetch(`/libraryItem/${item}`, {
                method: 'GET',
            })

            const data = await response.json()
            this.selectedPlaylistId = data.libraryItem.item_id

            if (data.isRelease) {
                await this.getRelease(data.libraryItem.item_id)

                this.libraryItem = null;
            } else {
                await this.getPlaylist(data.libraryItem.item_id)
            }
        },
        async addTrackToLikes(track) {
            const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');

            const response = await fetch(`/track/${track}/add`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                }
            })

            const data = await response.json()
        },
        async addReleaseToLikes(release) {
            const token = document.querySelector('meta[name=csrf-token]').getAttribute('content');

            const response = await fetch(`/release/${release}/add`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                }
            })

            const data = await response.json();
            this.isReleaseLiked = data.liked;
        },
        selectLibraryItem(id) {
            this.selectedLibraryItem = id;
            this.getLibraryItem(id);
        }
    }));
})

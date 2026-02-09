document.addEventListener('alpine:init', () => {
    Alpine.data('artistStudio', () => ({
        setEditor: false,
        isCoverLoaded: false,
        coverPreview: null,
        editingTrack: null,
        editingRelease: null,
        tracks: [],
        releaseType: null,
        selectedView: 'tracks',

        getTracks() {
            return this.tracks.map((file, index) => ({
                title: this.tracks[index]?.title,
                file: file,
            }));
        },
        setPreview (e) {
            const file = e.target.files[0];
            if (!file) return;

            this.isCoverLoaded = true;
            this.coverPreview = URL.createObjectURL(file);
        },
        handleTrackUpload(e) {
            const selectedFiles = Array.from(e.target.files);

            this.tracks = selectedFiles.map((file, index) => ({
                originalIndex: index,
                file: file,
                title: file.name
            }));

            const order = Array.from(this.tracks.map(el => el.originalIndex));
            this.$refs.orderInput.value = order.join(', ');

            this.$nextTick(() => this.updateOrder());

            this.releaseType = this.tracks.length === 1 ? 'single' : 'album'
        },
        updateOrder() {
            const newOrder = Array.from(this.$el.querySelectorAll('[x-sort\\:item]'))
                .map(el => el.getAttribute('data-original-index'));

            this.$refs.orderInput.value = newOrder.join(',');
        }
    }))
})

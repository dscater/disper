<template>
    <div
        class="dropzone"
        @dragover.stop="handleDragOver"
        @dragleave.stop="handleDragLeave"
        @drop.stop="handleDrop"
        @click="openFilePicker"
    >
        <v-virtual-scroll
            :height="
                archivos_existentes.length > 0
                    ? archivos_existentes.length >= 1 &&
                      archivos_existentes.length <= 3
                        ? 300
                        : 500
                    : 0
            "
            :items="archivos_existentes"
            class="archivos_cargados w-100"
        >
            <template v-slot:default="{ item, index }">
                <div class="archivo">
                    <button
                        type="button"
                        class="btn_quitar"
                        @click.stop="quitarArchivo(index)"
                    >
                        <i class="mdi mdi-close"></i>
                    </button>
                    <div class="thumbail">
                        <img :src="item.url_file" alt="Icon" />
                    </div>
                    <div class="info_archivo">
                        <div class="nombre">
                            {{ item.name }}
                        </div>
                    </div>
                </div>
            </template>
        </v-virtual-scroll>
        <div class="contenedor_info">
            <div v-if="!dragging" class="msj_drag">
                Arrastra y suelta archivos aquí o haz clic para seleccionar
                archivos
            </div>
            <div v-else class="zona_drop">Suelta los archivos aquí</div>
        </div>
        <input
            type="file"
            multiple
            style="display: none"
            ref="fileInput"
            id="fileInput"
            @change="handleFiles"
        />
    </div>
</template>

<script>
export default {
    props: {
        files: {
            type: Array,
            default: [],
        },
        maximo: {
            type: Number,
            default: 10,
        },
    },
    data() {
        return {
            dragging: false,
            archivos_existentes: this.files,
            eliminados: [],
        };
    },
    methods: {
        quitarArchivo(index) {
            if (this.archivos_existentes[index].id != 0) {
                // existente en BD
                this.eliminados.push(this.archivos_existentes[index].id);
                this.$emit("addEliminados", this.eliminados);
            }
            this.archivos_existentes.splice(index, 1);
            this.$emit("UpdateFiles", this.archivos_existentes);
        },
        handleDrop(event) {
            event.preventDefault();
            this.dragging = false;
            const files = event.dataTransfer.files;
            this.handleFiles(files);
        },
        handleFiles(eventOrFiles) {
            let files = [];
            if (eventOrFiles instanceof Event) {
                // Si se inició la carga mediante clic
                files = eventOrFiles.target.files;
            } else {
                // Si se inició la carga mediante arrastrar y soltar
                files = eventOrFiles;
            }
            let total_cargados =
                parseInt(files.length) +
                parseInt(this.archivos_existentes.length);
            console.log(total_cargados);
            if (total_cargados <= this.maximo) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    this.generateThumbnail(file);
                }
                this.$refs.fileInput.value = null;
            } else {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `No es posible cargar mas de ${this.maximo} imagénes`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            }
        },
        openFilePicker() {
            // Simula el clic en el input de tipo file
            this.$refs.fileInput.click();
        },
        handleDragOver(event) {
            event.preventDefault();
            this.dragging = true;
        },
        handleDragLeave(event) {
            event.preventDefault();
            this.dragging = false;
        },
        generateThumbnail(file) {
            const reader = new FileReader();
            if (file.type.startsWith("image/")) {
                reader.onload = (e) => {
                    this.archivos_existentes.push({
                        id: 0,
                        name: file.name,
                        url_file: e.target.result,
                        file: file,
                    });
                    this.$emit("UpdateFiles", [...this.archivos_existentes]);
                };
            } else {
                this.archivos_existentes.push({
                    id: 0,
                    name: file.name,
                    url_file: url_assets + "/imgs/attach.png",
                    file: file,
                });
                this.$emit("UpdateFiles", [...this.archivos_existentes]);
            }
            reader.readAsDataURL(file);
        },
    },
};
</script>

<style scoped>
.dropzone {
    padding: 20px;
    width: 100%;
    border: dotted 2px black;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.dropzone .archivos {
    width: 100%;
    text-align: center;
}

.dropzone button {
    width: 20%;
    margin: auto;
    margin-top: 20px;
}

.dropzone .contenedor_info {
    text-align: center;
    width: 100%;
}
.dropzone .msj_drag,
.dropzone .zona_drop {
    padding: 20px;
    width: 100%;
    height: 100%;
    background: #ececec;
}

.dropzone .zona_drop {
    background-color: rgb(145, 255, 231);
}

.archivos_cargados {
    justify-content: center;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 15px;
}

.v-virtual-scroll__container {
    width: 100%;
    display: flex !important;
}

.archivos_cargados .archivo {
    position: relative;
    width: 200px;
    padding: 20px;
}

.archivos_cargados .archivo .thumbail {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    width: 100%;
    overflow: hidden;
    height: 180px;
}
.archivos_cargados .archivo .thumbail img {
    height: 180px;
}

.archivos_cargados .archivo .info_archivo {
    width: 100%;
}
.archivos_cargados .archivo .info_archivo .nombre {
    font-size: 0.9em;
}

.archivos_cargados .archivo .btn_quitar {
    position: absolute;
    margin: 0px;
    top: 10px;
    right: 20px;
    font-size: 1.3em;
}

.archivos_cargados .archivo .btn_quitar:hover {
    background-color: rgb(250, 210, 210);
    color: red;
}
</style>

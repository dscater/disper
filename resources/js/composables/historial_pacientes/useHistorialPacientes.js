import axios from "axios";
import { onMounted, reactive } from "vue";
import { usePage } from "@inertiajs/vue3";

const oHistorialPaciente = reactive({
    id: 0,
    paciente_id: null,
    fecha_pc: "",
    meses_dpc: "",
    mamografia_aa: "",
    biopsias_mp: "",
    hiperplasia_a: "",
    cancer_mp: "",
    lado_ta: "",
    cancer_olc: "",
    parientes_pgcm: "",
    otros_pccm: "",
    parientes_cco: "",
    parientes_ccc: "",
    parientes_cce: "",
    parientes_cotc: "",
    historial_archivos: reactive([]),
    _method: "POST",
});

export const useHistorialPacientes = () => {
    const { flash } = usePage().props;
    const getHistorialPacientes = async () => {
        try {
            const response = await axios.get(
                route("historial_pacientes.listado"),
                {
                    headers: { Accept: "application/json" },
                }
            );
            return response.data.historial_pacientes;
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.response?.data
                        ? err.response?.data?.message
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            throw err; // Puedes manejar el error según tus necesidades
        }
    };

    const getHistorialPacientesApi = async (data) => {
        try {
            const response = await axios.get(
                route("historial_pacientes.paginado", data),
                {
                    headers: { Accept: "application/json" },
                }
            );
            return response.data.historial_pacientes;
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.response?.data
                        ? err.response?.data?.message
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            throw err; // Puedes manejar el error según tus necesidades
        }
    };
    const saveHistorialPaciente = async (data) => {
        try {
            const response = await axios.post(
                route("historial_pacientes.store", data),
                {
                    headers: { Accept: "application/json" },
                }
            );
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            return response.data;
        } catch (err) {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            console.error("Error:", err);
            throw err; // Puedes manejar el error según tus necesidades
        }
    };

    const deleteHistorialPaciente = async (id) => {
        try {
            const response = await axios.delete(
                route("historial_pacientes.destroy", id),
                {
                    headers: { Accept: "application/json" },
                }
            );
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            return response.data;
        } catch (err) {
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            console.error("Error:", err);
            throw err; // Puedes manejar el error según tus necesidades
        }
    };

    const setHistorialPaciente = (item = null, archivos = false) => {
        if (item) {
            oHistorialPaciente.id = item.id;
            oHistorialPaciente.paciente_id = item.paciente_id;
            oHistorialPaciente.fecha_pc = item.fecha_pc;
            oHistorialPaciente.meses_dpc = item.meses_dpc;
            oHistorialPaciente.mamografia_aa = item.mamografia_aa;
            oHistorialPaciente.biopsias_mp = item.biopsias_mp;
            oHistorialPaciente.hiperplasia_a = item.hiperplasia_a;
            oHistorialPaciente.cancer_mp = item.cancer_mp;
            oHistorialPaciente.lado_ta = item.lado_ta;
            oHistorialPaciente.cancer_olc = item.cancer_olc;
            oHistorialPaciente.parientes_pgcm = item.parientes_pgcm;
            oHistorialPaciente.otros_pccm = item.otros_pccm;
            oHistorialPaciente.parientes_cco = item.parientes_cco;
            oHistorialPaciente.parientes_ccc = item.parientes_ccc;
            oHistorialPaciente.parientes_cce = item.parientes_cce;
            oHistorialPaciente.parientes_cotc = item.parientes_cotc;
            oHistorialPaciente.historial_archivos = reactive([]);
            if (archivos) {
                oHistorialPaciente.historial_archivos = reactive([
                    ...item.historial_archivos,
                ]);
            }
            oHistorialPaciente._method = "PUT";
            return oHistorialPaciente;
        }
        return false;
    };

    const limpiarHistorialPaciente = () => {
        oHistorialPaciente.id = 0;
        oHistorialPaciente.paciente_id = null;
        oHistorialPaciente.fecha_pc = "";
        oHistorialPaciente.meses_dpc = "";
        oHistorialPaciente.mamografia_aa = "";
        oHistorialPaciente.biopsias_mp = "";
        oHistorialPaciente.hiperplasia_a = "";
        oHistorialPaciente.cancer_mp = "";
        oHistorialPaciente.lado_ta = "";
        oHistorialPaciente.cancer_olc = "";
        oHistorialPaciente.parientes_pgcm = "";
        oHistorialPaciente.otros_pccm = "";
        oHistorialPaciente.parientes_cco = "";
        oHistorialPaciente.parientes_ccc = "";
        oHistorialPaciente.parientes_cce = "";
        oHistorialPaciente.parientes_cotc = "";
        oHistorialPaciente.historial_archivos = reactive([]);
        oHistorialPaciente._method = "POST";
    };

    onMounted(() => {});

    return {
        oHistorialPaciente,
        getHistorialPacientes,
        getHistorialPacientesApi,
        saveHistorialPaciente,
        deleteHistorialPaciente,
        setHistorialPaciente,
        limpiarHistorialPaciente,
    };
};

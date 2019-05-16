export default {
    state: {
        error: '',
        sites: [],
        site: {
            name: '',
        },
        current: {},
    },
    mutations: {
        setError: (state, error) => {
            state.error = error;
        },
        setSites: (state, sites) => {
            state.sites = sites;
        },
        setEditorSite: (state, id) => {
            const site = {...state.sites.find(s => s.id === id)};

            // Shitty hack because SemanticUI-Vue doesn't support a simple
            // goddamn Number value for its sui-checkbox component. Wtf?!
            if (site.redirect_type) {
                site.redirect_type = site.redirect_type.toString();
            }

            state.current = site;
        },
        addSite: (state, site) => {
            state.sites.push(site);
            state.site.name = '';
        },
        updateSite: (state, {id, site}) => {
            let index = state.sites.findIndex(s => s.id === id);

            Vue.set(state.sites, index, site);
        },
    },
    actions: {
        loadSites: ({commit}) => {
            return new Promise((resolve, reject) =>
                axios.get('/api/sites') .then(response => {
                    commit('setSites', response.data);
                    resolve(response);
                }).catch(error => reject(error))
            );
        },
        editSite: ({commit}, site) => {
            commit('setEditorSite', site);
        },
        createSite: ({commit, state}) => {
            axios.post('/api/sites', state.site).then(response => {
                commit('addSite', response.data);
            });
        },
        updateSite: ({commit}, site) => {
            axios.put('/api/sites/'+site.id, site.data).then(response => {
                commit('updateSite', {
                    id: site.id,
                    site: response.data
                });
            }).catch(error => {
                const res = error.response;

                if (res && res.status === 422) {
                    commit('setError', res.data.message);
                } else if (res) {
                    commit('setError', res.statusText);
                } else {
                    commit('setError', error.message);
                }
            });
        },
    },
    getters: {
        sites: state => {
            return state.sites;
        },
    },
}

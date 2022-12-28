import Comment from "../../model/Comment";
import {Commit, Dispatch} from "vuex";
import CommentCreate from "../../payload/CommentCreate";
import CommentUpdate from "../../payload/CommentUpdate";
import CommentBuilder from "../../model/builder/CommentBuilder";
import ApiResponseCollectionBuilder from "../../model/builder/ApiResponseCollectionBuilder";
import CommentFilter from "../../model/filters/CommentFilter";

interface State {
    items: Comment[]
    totalItems: number
    page: number
    filter?: CommentFilter
}

const state: State = {
    items: [],
    totalItems: 0,
    page: 0,
};

const actions = {
    async create({dispatch, state}: {dispatch: Dispatch, state: State}, payload: CommentCreate) {
        return await fetch('/api/comments', {
            method: 'POST',
            body: JSON.stringify(payload),
        }).then((response) => {
            response.json().then(() => {
                if (state.filter) {
                    const filter = new CommentFilter()
                    Object.assign(filter, state.filter)
                    filter.page = 1

                    dispatch('loadByGroupKey', filter)
                }
            })
        })
    },
    async patch({dispatch}: {dispatch: Dispatch}, payload: {payload: CommentUpdate, id: string}) {
        return await fetch('/api/comments/'+payload.id, {
            method: 'PATCH',
            body: JSON.stringify(payload.payload),
        })
    },
    async loadByGroupKey({commit, state}: {commit: Commit, state: State}, filter: CommentFilter) {
        commit('SET_FILTER', filter)

        const url = new URL('/api/comments', location.origin)
        filter.buildUrlSearchParams(url)

        return await fetch(url).then((response) => {
            return response.json().then((data) => {
                const builder = new ApiResponseCollectionBuilder<Comment>(new CommentBuilder())
                const collection = builder.FromJSON(data)

                commit('SET_TOTAL_ITEMS', collection.totalItems)
                commit('SET_PAGE', collection.page)
                commit('SET_ITEM', collection.items)
            })
        })
    },
    async loadNextPage({commit, state}: {commit: Commit, state: State}) {
        commit('INCREMENT_PAGE_FILTER')
        const filter = state.filter
        if (!filter) {
            return;
        }

        const url = new URL('/api/comments', location.origin)
        filter.buildUrlSearchParams(url)

        return await fetch(url).then((response) => {
            return response.json().then((data) => {
                const builder = new ApiResponseCollectionBuilder<Comment>(new CommentBuilder())
                const collection = builder.FromJSON(data)

                commit('SET_TOTAL_ITEMS', collection.totalItems)
                commit('SET_PAGE', collection.page)
                commit('ADD_ITEM', collection.items)
            })
        })
    },
};

const mutations = {
    SET_ITEM(state: State, values: Comment[]) {
        state.items = values
    },
    ADD_ITEM(state: State, values: Comment[]) {
        state.items = [...state.items, ...values]
    },
    SET_TOTAL_ITEMS(state: State, values: number) {
        state.totalItems = values
    },
    SET_PAGE(state: State, values: number) {
        state.page = values
    },
    SET_FILTER(state: State, values: CommentFilter) {
        state.filter = values
    },
    INCREMENT_PAGE_FILTER(state: State) {
        if (state.filter) {
            state.filter.page++
        } else {
            state.filter = new CommentFilter()
        }
    },
};

const getters = {
    restToLoad(state: State) {
        return state.totalItems - state.items.length
    }
}

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};

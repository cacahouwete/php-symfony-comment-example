export default class CommentFilter {
    groupKey: string | null = null
    'order[createdAt]': string | null = null
    'exists[parent]': string | null = null
    page: number = 1
    itemsPerPage: number = 3

    public buildUrlSearchParams(url: URL): void {
        let property: keyof typeof this
        for (property in this) {
            const value = this[property]
            if (value === null || value === undefined) {
                continue
            }

            url.searchParams.set(property, value.toString())
        }
    }
}

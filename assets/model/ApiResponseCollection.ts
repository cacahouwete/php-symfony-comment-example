export default class ApiResponseCollection<T> {
    public items: T[]
    public totalItems: number
    public page: number
    public itemsPerPage: number

    constructor(items: T[], totalItems: number, page: number, itemsPerPage: number) {
        this.items = items;
        this.totalItems = totalItems;
        this.page = page;
        this.itemsPerPage = itemsPerPage;
    }
}

import ApiResponseCollection from "../ApiResponseCollection";
import IModelBuilder from "./IModelBuilder";

export default class ApiResponseCollectionBuilder<T> implements IModelBuilder<ApiResponseCollection<T>> {
    private entityBuilder: IModelBuilder<T>

    constructor(entityBuilder: IModelBuilder<T>) {
        this.entityBuilder = entityBuilder;
    }

    FromJSON(json: any): ApiResponseCollection<T> {
        return new ApiResponseCollection<T>(
            json.items.map((item: any) => this.entityBuilder.FromJSON(item)),
            json.totalItems,
            json.page,
            json.itemsPerPage,
        )
    }
}

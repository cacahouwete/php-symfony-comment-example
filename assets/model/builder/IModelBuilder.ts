export default interface IModelBuilder<T> {
    FromJSON(json: any): T
}

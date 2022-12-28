import Comment from "../Comment";
import IModelBuilder from "./IModelBuilder";

export default class CommentBuilder implements IModelBuilder<Comment> {
    FromJSON(json: any): Comment {
        return new Comment(
            json.id,
            json.content,
            new Date(json.createdAt),
            json.groupKey,
            json.rate,
            json.children ? json.children.map((item: any) => this.FromJSON(item)) : [],
            json.authorAvatar,
            json.authorUsername,
        )
    }
}

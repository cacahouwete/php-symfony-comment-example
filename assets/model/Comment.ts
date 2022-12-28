export default class Comment {
    public id: string
    public content: string
    public createdAt: Date
    public groupKey: string
    public rate: number | null = null
    public children: Comment[] = []
    public authorAvatar: string
    public authorUsername: string

    constructor(id: string, content: string, createdAt: Date, groupKey: string, rate: number | null, children: Comment[], authorAvatar: string, authorUsername: string) {
        this.id = id;
        this.content = content;
        this.createdAt = createdAt;
        this.groupKey = groupKey;
        this.rate = rate;
        this.children = children;
        this.authorAvatar = authorAvatar;
        this.authorUsername = authorUsername;
    }
}

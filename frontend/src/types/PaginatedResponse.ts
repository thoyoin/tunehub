export interface PaginatedResponse {
    current_page: number;
    data: T[];
    first_page_url: string;
    last_page_url: string;
    from: number;
    to: number;
    total: number;
    last_page: number;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: ?string;
}

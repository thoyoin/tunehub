interface EditingProductVariant {
    id?: number | string;
    variant_name: string;
    price: number;
    stock: number;
}

interface ProductImage {
    id: number;
    product_id: number;
    image_url: string;
    created_at: string;
    updated_at: string;
}

export interface ArtistMerch {
    id: number,
    slug: string,
    title: string,
    description: string,
    user_id: number,
    status: string,
    cover_url: string,
    currency: string,
    product_variants: EditingProductVariant[],
    product_images: ProductImage[],
}

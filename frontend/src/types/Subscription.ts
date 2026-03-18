export interface Subscription {
    current_period_start: string
    current_period_end: string
    next_billing: string
    amount: number
    interval: string
    currency: string
    card: {
        brand: string
        last4: string
        country: string
        funding: string
        exp_month: string
        exp_year: string
    }
}

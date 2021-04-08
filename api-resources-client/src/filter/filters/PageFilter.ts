import { Filter, FilterJSON } from '../Filter'

export type PageFilterJSON = FilterJSON & {
  default_page_size: number,
  page_sizes: number[]
}

export class PageFilter extends Filter {
  public defaultPageSize: number
  public pageSizes: number[]

  constructor (json: PageFilterJSON) {
    super(json)

    this.defaultPageSize = json.default_page_size
    this.pageSizes = json.page_sizes
  }
}
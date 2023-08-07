// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!

import axios from 'axios';

export type Paginated<T> = {
  items: T[];
  totalCount: number;
  pagesCount: number;
  page: number;
};

export type CollectionResponse<Resource extends {id: string}> = {
  'hydra:member': Resource[];
  'hydra:totalItems': number;
  'hydra:view': { '@id': string; 'hydra:last': string };
  'hydra:search': { 'hydra:mapping': any[] };
  'hydra:last': string;
};
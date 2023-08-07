// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!

import axios from 'axios';

export type JsonResponse<T> = {
  data: T;
};

export type UserOutput = {
  id: string;
};

export type UserShortOutput = {
  id: string;
};

export const apiAnnotationsReturnGet = (): Promise<UserOutput> => {
  return axios
    .get<UserOutput>(`/api/annotations-return`)
    .then((response) => response.data);
}

export const apiAnnotationsReturnPrecedenceGet = (): Promise<UserShortOutput> => {
  return axios
    .get<UserShortOutput>(`/api/annotations-return-precedence`)
    .then((response) => response.data);
}

export const apiNestedGenericsSimpleTypeGet = (): Promise<JsonResponse<string[]>> => {
  return axios
    .get<JsonResponse<string[]>>(`/api/nested-generics-simple-type`)
    .then((response) => response.data);
}

export const apiRouteWithNestedGenericsAnnotationsReturnGet = (): Promise<JsonResponse<UserOutput>> => {
  return axios
    .get<JsonResponse<UserOutput>>(`/api/route-with-nested-generics-annotations-return`)
    .then((response) => response.data);
}

export const apiRouteWithNestedGenericsUnionAnnotationsReturnGet = (): Promise<JsonResponse<UserOutput[]>> => {
  return axios
    .get<JsonResponse<UserOutput[]>>(`/api/route-with-nested-generics-union-annotations-return`)
    .then((response) => response.data);
}

export type CollectionResponse<Resource extends {id: string}> = {
  'hydra:member': Resource[];
  'hydra:totalItems': number;
  'hydra:view': { '@id': string; 'hydra:last': string };
  'hydra:search': { 'hydra:mapping': any[] };
  'hydra:last': string;
};
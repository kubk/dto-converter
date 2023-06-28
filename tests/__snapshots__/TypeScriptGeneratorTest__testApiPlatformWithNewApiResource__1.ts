// THE FILE WAS AUTOGENERATED USING DTO-CONVERTER. PLEASE DO NOT EDIT IT!

import axios from 'axios';

export type BranchContextUpdateInput = {
};

export type FullBookingOutput = {
  id: string;
};

export type HubUserCreateInput = {
  id: string;
  username: string;
};

export type HubUserOutput = {
  id: string;
  username: string;
};

export type HubUserUpdateInput = {
  username: string;
};

export type JobSheetTagCollectionOutput = {
};

export const apiBookingsGet = (filters: any | null = null): Promise<CollectionResponse<FullBookingOutput>> => {
  return axios
    .get<CollectionResponse<FullBookingOutput>>(`/api/bookings`, { params: filters })
    .then((response) => response.data);
}

export const apiCustomerSitesIdBookingsGet = (id: string, filters: any | null = null): Promise<CollectionResponse<FullBookingOutput>> => {
  return axios
    .get<CollectionResponse<FullBookingOutput>>(`/api/customer_sites/${id}/bookings`, { params: filters })
    .then((response) => response.data);
}

export const apiHubUsersGet = (filters: any | null = null): Promise<CollectionResponse<HubUserOutput>> => {
  return axios
    .get<CollectionResponse<HubUserOutput>>(`/api/hub_users`, { params: filters })
    .then((response) => response.data);
}

export const apiHubUsersIdPut = (id: string, body: HubUserUpdateInput): Promise<HubUserOutput> => {
  return axios
    .put<HubUserOutput>(`/api/hub_users/${id}`, body)
    .then((response) => response.data);
}

export const apiHubUsersIdGet = (id: string): Promise<HubUserOutput> => {
  return axios
    .get<HubUserOutput>(`/api/hub_users/${id}`)
    .then((response) => response.data);
}

export const apiHubUsersIdDelete = (id: string): Promise<HubUserOutput> => {
  return axios
    .delete<HubUserOutput>(`/api/hub_users/${id}`)
    .then((response) => response.data);
}

export const apiHubUsersIdPost = (id: string, body: HubUserCreateInput): Promise<HubUserOutput> => {
  return axios
    .post<HubUserOutput>(`/api/hub_users/${id}`, body)
    .then((response) => response.data);
}

export const apiHubUsersIdUpdateBranchContextPut = (id: string, body: BranchContextUpdateInput): Promise<HubUserOutput> => {
  return axios
    .put<HubUserOutput>(`/api/hub_users/${id}/update_branch_context`, body)
    .then((response) => response.data);
}

export const apiJobSheetsIdTagsGet = (id: string): Promise<JobSheetTagCollectionOutput> => {
  return axios
    .get<JobSheetTagCollectionOutput>(`/api/job_sheets/${id}/tags`)
    .then((response) => response.data);
}

export type CollectionResponse<Resource extends {id: string}> = {
  'hydra:member': Resource[];
  'hydra:totalItems': number;
  'hydra:view': { '@id': string; 'hydra:last': string };
  'hydra:search': { 'hydra:mapping': any[] };
  'hydra:last': string;
};
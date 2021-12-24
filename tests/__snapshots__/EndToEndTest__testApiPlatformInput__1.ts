export enum ColorEnum {
  RED = 0,
  GREEN = 1,
  BLUE = 2,
}

export type GenderEnum = null | 0 | 1;

export type ProfileOutput = {
  firstName: string;
  lastName: string;
  gender: GenderEnum;
  color: ColorEnum;
};

export type UserCreateInput = {
  profile: string;
  promotedAt: string | null;
  userTheme: { value: ColorEnum };
  industriesUnion: string[] | null;
  industriesNullable: string[] | null;
  money: { currency: string; amount: number };
  gender: { value: GenderEnum };
  location: { lat: string; lan: string };
};
